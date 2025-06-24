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
     *
     * @return JsonResponse
     */
    public function getAllMessages(): JsonResponse
    {
        // Ambil semua pesan yang sudah disetujui, urutkan dari yang terbaru
        $messages = InvitationMessage::where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->with('contact:id,name,username') // Eager load contact dengan kolom terbatas
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Mendapatkan semua ucapan untuk kontak dengan username tertentu.
     *
     * @param string $username
     * @return JsonResponse
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
     *
     * @param Request $request
     * @return JsonResponse
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

        Log::info('Contact found', ['contact_id' => $contact->id]);

        // Buat pesan ucapan baru
        $invitationMessage = InvitationMessage::create([
            'contact_id' => $contact->id,
            'name' => $validated['name'],
            'message' => $validated['message'],
            'attendance' => $validated['attendance'],
            'is_approved' => true, // Defaultnya disetujui
        ]);

        Log::info('Invitation message created', ['message_id' => $invitationMessage->id]);

        // Kirim feedback melalui WhatsApp
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
     *
     * @param Contact $contact
     * @param InvitationMessage $message
     * @return void
     */
    private function sendWhatsAppFeedback(Contact $contact, InvitationMessage $message): void
    {
        try {
            // Cari admin dari kontak ini
            $admin = $contact->admin;

            if (!$admin || empty($admin->whatsapp_api_key)) {
                Log::warning('Tidak dapat mengirim feedback WhatsApp: API key tidak ditemukan');
                return;
            }

            // Siapkan pesan feedback
            $feedbackMessage = $this->prepareFeedbackMessage($contact, $message);

            // Kirim pesan WhatsApp
            $result = $this->whatsappService->sendMessage(
                $admin->whatsapp_api_key,
                $contact->phone_number,
                $feedbackMessage,
                $contact->name,
                $contact->country_code ?: '62' // Default ke Indonesia jika country code tidak ada
            );

            // Log hasil pengiriman
            if ($result['success']) {
                Log::info('Feedback WhatsApp berhasil dikirim', [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone_number,
                    'country_code' => $contact->country_code,
                ]);
            } else {
                Log::error('Gagal mengirim feedback WhatsApp', [
                    'contact_id' => $contact->id,
                    'phone' => $contact->phone_number,
                    'country_code' => $contact->country_code,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error saat mengirim feedback WhatsApp', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Menyiapkan pesan feedback WhatsApp
     *
     * @param Contact $contact
     * @param InvitationMessage $message
     * @return string
     */
    private function prepareFeedbackMessage(Contact $contact, InvitationMessage $message): string
    {
        // Ambil data admin (mempelai pria dan wanita)
        $groomAdmin = \App\Models\Admin::where('role', 'groom')->first();
        $brideAdmin = \App\Models\Admin::where('role', 'bride')->first();

        $groomName = $groomAdmin ? $groomAdmin->name : 'Mempelai Pria';
        $brideName = $brideAdmin ? $brideAdmin->name : 'Mempelai Wanita';

        // Panggilan yang digunakan untuk kontak (dari kolom greeting)
        $panggilan = $contact->greeting ?: $contact->name;

        // Siapkan pesan feedback
        $feedback = "Makasih banyak ya {$panggilan} untuk ucapan dan doanya. ";
        $feedback .= "Kami bersyukur bisa dikelilingi orang yang berbahagia, serta turut mendoakan kebaikan kami dan untuk kebanyakan sekitar. ";
        $feedback .= "Tuhan memberkati, dan doa baik juga kembali pada mu ya {$panggilan} dengan sukacita dan kasih yang melimpah ya {$panggilan} {$contact->name}\n\n";
        $feedback .= "Salam hangat,\n{$groomName} & {$brideName}";

        return $feedback;
    }

    /**
     * Admin: Memperbarui status persetujuan pesan.
     *
     * @param Request $request
     * @param InvitationMessage $message
     * @return JsonResponse
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
     *
     * @param InvitationMessage $message
     * @return JsonResponse
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
    public function testBroadcast()
    {
        // Buat data dummy untuk mengirimkan ke event
        $invitationMessage = InvitationMessage::create([
            'username' => 'bahari',
            'name' => 'Bahari',
            'message' => 'Hello, this is a test message!',
            'attendance' => 'hadir',
            'is_approved' => true
        ]);

        // Trigger event ke Pusher
        event(new \App\Events\NewInvitationMessage($invitationMessage));

        return response()->json([
            'status' => 'success',
            'message' => 'Broadcast event sent successfully!',
            'data' => $invitationMessage
        ], 200);
    }

}
