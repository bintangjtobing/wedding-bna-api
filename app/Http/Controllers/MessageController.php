<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\Message;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function create()
    {
        $currentAdmin = Auth::guard('admin')->user();
        $groomAdmin = Admin::where('role', 'groom')->first();
        $brideAdmin = Admin::where('role', 'bride')->first();

        // Ambil statistik status undangan untuk ditampilkan
        $groomSentCount = $groomAdmin ? $groomAdmin->contacts()->where('invitation_status', 'terkirim')->count() : 0;
        $groomPendingCount = $groomAdmin ? $groomAdmin->contacts()->where('invitation_status', 'belum_dikirim')->count() : 0;
        $brideSentCount = $brideAdmin ? $brideAdmin->contacts()->where('invitation_status', 'terkirim')->count() : 0;
        $bridePendingCount = $brideAdmin ? $brideAdmin->contacts()->where('invitation_status', 'belum_dikirim')->count() : 0;

        return view('messages.create', compact(
            'currentAdmin',
            'groomAdmin',
            'brideAdmin',
            'groomSentCount',
            'groomPendingCount',
            'brideSentCount',
            'bridePendingCount'
        ));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'message_content' => 'required|string',
            'admin_selection' => 'required|array',
            'admin_selection.*' => 'exists:admins,id',
            'only_pending' => 'nullable|boolean',
        ]);

        // Simpan pesan
        $message = Message::create([
            'content' => $validated['message_content'],
        ]);

        $sentCount = 0;
        $failedCount = 0;
        $now = now();

        // Kirim pesan berdasarkan admin yang dipilih
        foreach ($validated['admin_selection'] as $adminId) {
            $admin = Admin::findOrFail($adminId);

            // Get contacts based on the filter
            $contactsQuery = $admin->contacts();

            // Jika opsi hanya kirim ke kontak belum terkirim diaktifkan
            if ($request->has('only_pending') && $request->only_pending) {
                $contactsQuery->where('invitation_status', 'belum_dikirim');
            }

            $contacts = $contactsQuery->get();

            foreach ($contacts as $contact) {
                // Buat log pesan
                $messageLog = MessageLog::create([
                    'message_id' => $message->id,
                    'contact_id' => $contact->id,
                    'admin_id' => $admin->id,
                    'status' => 'pending',
                ]);

                // Personalisasi pesan dengan mengganti [NAMA] dengan nama kontak
                $personalizedMessage = str_replace('[NAMA]', $contact->name, $validated['message_content']);

                // Kirim pesan via WhatsApp API
                $result = $this->sendWhatsAppMessage(
                    $admin->whatsapp_api_key,
                    $contact->phone_number,
                    $personalizedMessage
                );

                // Update status log pesan dan status undangan di kontak
                if ($result['success']) {
                    $messageLog->update([
                        'status' => 'sent',
                        'response' => json_encode($result['response']),
                    ]);

                    // Update status undangan di kontak
                    $contact->updateInvitationStatus('terkirim', $now);

                    $sentCount++;
                } else {
                    $messageLog->update([
                        'status' => 'failed',
                        'response' => json_encode($result['error']),
                    ]);

                    // Update status undangan di kontak sebagai gagal
                    $contact->updateInvitationStatus('gagal');

                    $failedCount++;
                }
            }
        }

        return redirect()->route('dashboard')
            ->with('success', "Pesan terkirim ke {$sentCount} kontak, gagal ke {$failedCount} kontak.");
    }

    // Fungsi untuk mengirim pesan WhatsApp
    private function sendWhatsAppMessage($apiKey, $phoneNumber, $message)
    {
        try {
            // Contoh implementasi, sesuaikan dengan provider WhatsApp API yang digunakan
            // Misalnya: Twilio, Chat API, dll.
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.whatsapp-provider.com/send', [
                'phone' => $phoneNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'response' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    // API endpoint untuk mengirim pesan undangan
    public function apiSendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'admin_selection' => 'required|array',
            'admin_selection.*' => 'exists:admins,id',
            'only_pending' => 'nullable|boolean',
        ]);

        $currentAdmin = Auth::guard('admin')->user();

        // Simpan pesan
        $message = Message::create([
            'content' => $validated['message'],
        ]);

        $sentCount = 0;
        $failedCount = 0;
        $logs = [];
        $now = now();

        // Kirim pesan berdasarkan admin yang dipilih
        foreach ($validated['admin_selection'] as $adminId) {
            $admin = Admin::findOrFail($adminId);

            // Get contacts based on the filter
            $contactsQuery = $admin->contacts();

            // Jika opsi hanya kirim ke kontak belum terkirim diaktifkan
            if ($request->has('only_pending') && $request->only_pending) {
                $contactsQuery->where('invitation_status', 'belum_dikirim');
            }

            $contacts = $contactsQuery->get();

            foreach ($contacts as $contact) {
                // Buat log pesan
                $messageLog = MessageLog::create([
                    'message_id' => $message->id,
                    'contact_id' => $contact->id,
                    'admin_id' => $admin->id,
                    'status' => 'pending',
                ]);

                // Personalisasi pesan dengan mengganti [NAMA] dengan nama kontak
                $personalizedMessage = str_replace('[NAMA]', $contact->name, $validated['message']);

                // Kirim pesan via WhatsApp API
                $result = $this->sendWhatsAppMessage(
                    $admin->whatsapp_api_key,
                    $contact->phone_number,
                    $personalizedMessage
                );

                // Update status log pesan dan status undangan di kontak
                if ($result['success']) {
                    $messageLog->update([
                        'status' => 'sent',
                        'response' => json_encode($result['response']),
                    ]);

                    // Update status undangan di kontak
                    $contact->updateInvitationStatus('terkirim', $now);

                    $sentCount++;
                } else {
                    $messageLog->update([
                        'status' => 'failed',
                        'response' => json_encode($result['error']),
                    ]);

                    // Update status undangan di kontak sebagai gagal
                    $contact->updateInvitationStatus('gagal');

                    $failedCount++;
                }

                $logs[] = [
                    'id' => $messageLog->id,
                    'contact' => $contact->name,
                    'phone' => $contact->phone_number,
                    'status' => $messageLog->status,
                    'invitation_status' => $contact->invitation_status,
                    'sent_at' => $contact->sent_at ? $contact->sent_at->format('Y-m-d H:i:s') : null,
                ];
            }
        }

    return response()->json([
        'status' => 'success',
        'message' => "Pesan terkirim ke {$sentCount} kontak, gagal ke {$failedCount} kontak.",
        'data' => [
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'logs' => $logs,
        ]
    ]);
    }
}
