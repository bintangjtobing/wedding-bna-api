<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\InvitationMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class InvitationMessageController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService = null)
    {
        $this->whatsappService = $whatsappService ?? app(WhatsAppService::class);
    }

    /**
     * Mendapatkan semua ucapan/doa yang sudah disetujui.
     */
    public function getAllMessages(): JsonResponse
    {
        $messages = InvitationMessage::where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->with('contact:id,name,username')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Mendapatkan semua ucapan untuk kontak dengan username tertentu.
     */
    public function getMessagesByUsername(string $username): JsonResponse
    {
        $contact = Contact::where('username', $username)->first();

        if (!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found',
            ], 404);
        }

        $messages = InvitationMessage::where('contact_id', $contact->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Menyimpan ucapan/doa baru.
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('Store method triggered', ['data' => $request->all()]);

        $validated = $request->validate([
            'username' => 'required|string|exists:contacts,username',
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'attendance' => 'required|in:hadir,tidak_hadir,belum_pasti',
        ]);

        Log::info('Validation passed', ['validated_data' => $validated]);

        // Cari kontak berdasarkan username
        $contact = Contact::where('username', $validated['username'])->first();

        if (!$contact) {
            Log::error('Contact not found', ['username' => $validated['username']]);
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found',
            ], 404);
        }

        Log::info('Contact found', ['contact_id' => $contact->id, 'country' => $contact->country]);

        // Buat pesan ucapan baru
        $invitationMessage = InvitationMessage::create([
            'contact_id' => $contact->id,
            'name' => $validated['name'],
            'message' => $validated['message'],
            'attendance' => $validated['attendance'],
            'is_approved' => true,
        ]);

        Log::info('Invitation message created', ['message_id' => $invitationMessage->id]);

        // Kirim feedback melalui WhatsApp dengan multi-language support
        $this->sendWhatsAppFeedback($contact, $invitationMessage);

        // Broadcast event untuk websocket
        try {
            event(new \App\Events\NewInvitationMessage($invitationMessage));
            Log::info('Event broadcasted');
        } catch (\Exception $e) {
            Log::error('Error broadcasting event', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send broadcast event.',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Ucapan telah berhasil dikirim. Terima kasih!',
            'data' => $invitationMessage
        ], 201);
    }

    /**
     * Mengirim pesan feedback WhatsApp ke pengirim ucapan
     * dengan dukungan multi-language berdasarkan negara
     */
    private function sendWhatsAppFeedback(Contact $contact, InvitationMessage $message): void
    {
        try {
            // Cari admin dari kontak ini
            $admin = $contact->admin;

            if (!$admin || empty($admin->whatsapp_api_key)) {
                Log::warning('Tidak dapat mengirim feedback WhatsApp: API key tidak ditemukan', [
                    'contact_id' => $contact->id,
                    'country' => $contact->country
                ]);
                return;
            }

            // Siapkan pesan feedback berdasarkan negara
            $feedbackMessage = $this->prepareFeedbackMessage($contact, $message);

            Log::info('Prepared feedback message', [
                'contact_id' => $contact->id,
                'country' => $contact->country,
                'language_used' => $this->getLanguageByCountry($contact->country),
                'message_length' => strlen($feedbackMessage)
            ]);

            // Kirim pesan WhatsApp
            $result = $this->whatsappService->sendMessage(
                $admin->whatsapp_api_key,
                $contact->phone_number,
                $feedbackMessage,
                $contact->name,
                $contact->country_code ?: '62'
            );

            // Log hasil pengiriman
            if ($result['success']) {
                Log::info('Multi-language feedback WhatsApp berhasil dikirim', [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone_number,
                    'country' => $contact->country,
                    'country_code' => $contact->country_code,
                    'language' => $this->getLanguageByCountry($contact->country)
                ]);
            } else {
                Log::error('Gagal mengirim multi-language feedback WhatsApp', [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone_number,
                    'country' => $contact->country,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error saat mengirim multi-language feedback WhatsApp', [
                'contact_id' => $contact->id,
                'country' => $contact->country,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Menyiapkan pesan feedback berdasarkan negara kontak
     * Simple: Indonesia = Bahasa Indonesia, Lainnya = English
     */
    private function prepareFeedbackMessage(Contact $contact, InvitationMessage $message): string
    {
        // Ambil data admin (mempelai pria dan wanita)
        $groomAdmin = \App\Models\Admin::where('role', 'groom')->first();
        $brideAdmin = \App\Models\Admin::where('role', 'bride')->first();

        $groomName = $groomAdmin ? $groomAdmin->name : 'Groom';
        $brideName = $brideAdmin ? $brideAdmin->name : 'Bride';

        // Panggilan yang digunakan untuk kontak
        $panggilan = $contact->greeting ?: $contact->name;
        $nama = $contact->name;

        // Cek apakah kontak dari Indonesia
        $isIndonesia = strtoupper($contact->country ?? '') === 'ID';

        if ($isIndonesia) {
            // Template Bahasa Indonesia
            $feedback = "Makasih banyak ya {$panggilan} untuk ucapan dan doanya. 🙏

Kami bersyukur bisa dikelilingi orang yang berbahagia, serta turut mendoakan kebaikan kami dan untuk kebanyakan sekitar.

Tuhan memberkati, dan doa baik juga kembali pada mu ya {$panggilan} dengan sukacita dan kasih yang melimpah 💕

Salam hangat,
{$groomName} & {$brideName}";
        } else {
            // Template Bahasa Inggris untuk semua negara lain
            $feedback = "Dear {$panggilan}, 🙏

Thank you so much for your wonderful wishes and prayers! We are truly blessed to have amazing people like you who share in our special day and send such beautiful thoughts our way.

Your kind words mean the world to us, and we feel so grateful to have you in our lives.

May God bless you abundantly, and may all the happiness and love return to you {$nama}! 💕

With love and gratitude,
{$groomName} & {$brideName}";
        }

        Log::info('Template selected for feedback', [
            'contact_id' => $contact->id,
            'country_code' => $contact->country,
            'is_indonesia' => $isIndonesia,
            'language_used' => $isIndonesia ? 'Indonesian' : 'English'
        ]);

        return $feedback;
    }

    /**
     * Mendapatkan informasi bahasa berdasarkan kode negara
     * Simple: ID = Indonesian, Lainnya = English
     */
    private function getLanguageByCountry(string $countryCode): string
    {
        return strtoupper($countryCode) === 'ID' ? 'Indonesian' : 'English';
    }

    /**
     * Admin: Memperbarui status persetujuan pesan.
     */
    public function updateApprovalStatus(Request $request, InvitationMessage $message): JsonResponse
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $validated = $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $oldStatus = $message->is_approved;
        $message->update([
            'is_approved' => $validated['is_approved'],
        ]);

        // Kirim feedback jika status berubah dari tidak disetujui menjadi disetujui
        if (!$oldStatus && $validated['is_approved']) {
            $contact = $message->contact;
            if ($contact) {
                $this->sendWhatsAppFeedback($contact, $message);
                event(new \App\Events\NewInvitationMessage($message));
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Status persetujuan pesan berhasil diperbarui.',
            'data' => $message
        ]);
    }

    /**
     * Admin: Menghapus pesan.
     */
    public function destroy(InvitationMessage $message): JsonResponse
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $message->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pesan berhasil dihapus.',
        ]);
    }

    /**
     * Test endpoint untuk broadcast
     */
    public function testBroadcast()
    {
        $invitationMessage = InvitationMessage::create([
            'username' => 'test-user',
            'name' => 'Test User',
            'message' => 'This is a test message for multi-language feedback!',
            'attendance' => 'hadir',
            'is_approved' => true
        ]);

        event(new \App\Events\NewInvitationMessage($invitationMessage));

        return response()->json([
            'status' => 'success',
            'message' => 'Multi-language broadcast event sent successfully!',
            'data' => $invitationMessage
        ], 200);
    }
}