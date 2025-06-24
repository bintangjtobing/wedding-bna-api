<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\Message;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppService;

class MessageController extends Controller
{
    protected $whatsappService;
    public function __construct(WhatsAppService $whatsappService = null)
    {
        $this->whatsappService = $whatsappService ?? app(WhatsAppService::class);
    }
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

        // Hitung statistik berdasarkan negara untuk semua admin
        $allContacts = collect();
        if ($groomAdmin) {
            $allContacts = $allContacts->merge($groomAdmin->contacts);
        }
        if ($brideAdmin) {
            $allContacts = $allContacts->merge($brideAdmin->contacts);
        }

        $indonesiaContactCount = $allContacts->where('country', 'ID')->count();
        $malaysiaContactCount = $allContacts->where('country', 'MY')->count();
        $singaporeContactCount = $allContacts->where('country', 'SG')->count();
        $otherCountryContactCount = $allContacts->whereNotIn('country', ['ID', 'MY', 'SG'])->count();

        return view('messages.create', compact(
            'currentAdmin',
            'groomAdmin',
            'brideAdmin',
            'groomSentCount',
            'groomPendingCount',
            'brideSentCount',
            'bridePendingCount',
            'indonesiaContactCount',
            'malaysiaContactCount',
            'singaporeContactCount',
            'otherCountryContactCount'
        ));
    }

    private function sendWhatsAppMessage($apiKey, $phoneNumber, $message, $recipientName = null, $countryCode = '62')
    {
        return $this->whatsappService->sendMessage($apiKey, $phoneNumber, $message, $recipientName, $countryCode);
    }

    public function send(Request $request)
{
    $validated = $request->validate([
        'message_content' => 'required|string',
        'admin_selection' => 'required|array',
        'admin_selection.*' => 'exists:admins,id',
        'only_pending' => 'nullable|boolean',
        'country_filter' => 'nullable|array',
        'country_filter.*' => 'in:ID,MY,SG,US,OTHER',
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

        // Filter berdasarkan negara jika dipilih
        if (!empty($validated['country_filter'])) {
            $contactsQuery->where(function($query) use ($validated) {
                foreach ($validated['country_filter'] as $country) {
                    if ($country === 'OTHER') {
                        // Untuk "OTHER", ambil negara selain ID, MY, SG
                        $query->orWhereNotIn('country', ['ID', 'MY', 'SG']);
                    } else {
                        $query->orWhere('country', $country);
                    }
                }
            });
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

            // Personalisasi pesan dengan mengganti placeholder dengan data kontak
            $originalMessage = $validated['message_content'];
            $personalizedMessage = $this->personalizeMessage($originalMessage, $contact);

            // Kirim pesan via WhatsApp API dengan country code yang benar
            $result = $this->sendWhatsAppMessage(
                $admin->whatsapp_api_key,
                $contact->phone_number,
                $personalizedMessage,
                $contact->name,
                $contact->country_code ?? '62' // Gunakan country_code dari contact
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

    // Buat pesan sukses yang lebih informatif
    $successMessage = "Pesan terkirim ke {$sentCount} kontak";
    if ($failedCount > 0) {
        $successMessage .= ", gagal ke {$failedCount} kontak";
    }

    // Tambahkan info filter jika ada
    if (!empty($validated['country_filter'])) {
        $countryNames = [
            'ID' => 'Indonesia',
            'MY' => 'Malaysia',
            'SG' => 'Singapura',
            'US' => 'Amerika Serikat',
            'OTHER' => 'Negara Lainnya'
        ];
        $selectedCountries = array_map(function($code) use ($countryNames) {
            return $countryNames[$code] ?? $code;
        }, $validated['country_filter']);

        $successMessage .= " (Filter: " . implode(', ', $selectedCountries) . ")";
    }

    return redirect()->route('dashboard')->with('success', $successMessage);
}

    public function resendFailed(Request $request)
    {
        $validated = $request->validate([
            'message_content' => 'required|string',
            'admin_selection' => 'required|array',
            'admin_selection.*' => 'exists:admins,id',
            'country_filter' => 'nullable|array', // Tambahan untuk filter negara
            'country_filter.*' => 'in:ID,MY,SG,US,OTHER',
        ]);

        // Simpan pesan baru
        $message = Message::create([
            'content' => $validated['message_content'],
        ]);

        $sentCount = 0;
        $failedCount = 0;
        $now = now();

        // Kirim pesan berdasarkan admin yang dipilih
        foreach ($validated['admin_selection'] as $adminId) {
            $admin = Admin::findOrFail($adminId);

            // Ambil kontak yang sebelumnya gagal
            $contactsQuery = $admin->contacts()->where('invitation_status', 'gagal');

            // TAMBAHAN: Filter berdasarkan negara jika dipilih (sama seperti di method send)
            if ($request->has('country_filter') && !empty($request->country_filter)) {
                $countries = $request->country_filter;

                if (in_array('OTHER', $countries)) {
                    $specificCountries = array_diff($countries, ['OTHER']);

                    if (!empty($specificCountries)) {
                        $contactsQuery->where(function($query) use ($specificCountries) {
                            $query->whereIn('country', $specificCountries)
                                  ->orWhereNotIn('country', ['ID', 'MY', 'SG', 'US']);
                        });
                    } else {
                        $contactsQuery->whereNotIn('country', ['ID', 'MY', 'SG', 'US']);
                    }
                } else {
                    $contactsQuery->whereIn('country', $countries);
                }
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

                // Personalisasi pesan dengan data kontak
                $originalMessage = $validated['message_content'];
                $personalizedMessage = $this->personalizeMessage($originalMessage, $contact);

                // Kirim pesan via WhatsApp API
                $result = $this->sendWhatsAppMessage(
                    $admin->whatsapp_api_key,
                    $contact->phone_number,
                    $personalizedMessage,
                    $contact->name,
                    $contact->country_code
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

                    // Update status undangan di kontak tetap gagal
                    $contact->updateInvitationStatus('gagal');

                    $failedCount++;
                }
            }
        }

        return redirect()->route('dashboard')
            ->with('success', "Pengiriman ulang berhasil: {$sentCount} terkirim, {$failedCount} gagal.");
    }

    // API endpoint untuk mengirim pesan undangan
    public function apiSendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'admin_selection' => 'required|array',
            'admin_selection.*' => 'exists:admins,id',
            'only_pending' => 'nullable|boolean',
            'country_filter' => 'nullable|array', // Tambahan untuk filter negara
            'country_filter.*' => 'in:ID,MY,SG,US,OTHER',
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

            // TAMBAHAN: Filter berdasarkan negara jika dipilih (sama seperti method send)
            if ($request->has('country_filter') && !empty($request->country_filter)) {
                $countries = $request->country_filter;

                if (in_array('OTHER', $countries)) {
                    $specificCountries = array_diff($countries, ['OTHER']);

                    if (!empty($specificCountries)) {
                        $contactsQuery->where(function($query) use ($specificCountries) {
                            $query->whereIn('country', $specificCountries)
                                ->orWhereNotIn('country', ['ID', 'MY', 'SG', 'US']);
                        });
                    } else {
                        $contactsQuery->whereNotIn('country', ['ID', 'MY', 'SG', 'US']);
                    }
                } else {
                    $contactsQuery->whereIn('country', $countries);
                }
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

                // Personalisasi pesan dengan mengganti placeholder dengan data kontak
                $originalMessage = $validated['message'];
                $personalizedMessage = $this->personalizeMessage($originalMessage, $contact);

                // Kirim pesan via WhatsApp API
                $result = $this->sendWhatsAppMessage(
                    $admin->whatsapp_api_key,
                    $contact->phone_number,
                    $personalizedMessage,
                    $contact->name,
                    $contact->country_code
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
                    'country' => $contact->country,
                    'status' => $messageLog->status,
                    'invitation_status' => $contact->invitation_status,
                    'sent_at' => $contact->sent_at ? $contact->sent_at->format('Y-m-d H:i:s') : null,
                ];
            }
        }

        // Siapkan response dengan info filter negara
        $responseMessage = "Pesan terkirim ke {$sentCount} kontak, gagal ke {$failedCount} kontak.";

        if ($request->has('country_filter') && !empty($request->country_filter)) {
            $countryNames = [
                'ID' => 'Indonesia',
                'MY' => 'Malaysia',
                'SG' => 'Singapura',
                'US' => 'Amerika Serikat',
                'OTHER' => 'Negara Lainnya'
            ];

            $selectedCountries = array_map(function($code) use ($countryNames) {
                return $countryNames[$code] ?? $code;
            }, $request->country_filter);

            $responseMessage .= " Filter negara: " . implode(', ', $selectedCountries) . ".";
        }

        return response()->json([
            'status' => 'success',
            'message' => $responseMessage,
            'data' => [
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'filtered_countries' => $request->country_filter ?? [],
                'total_processed' => $sentCount + $failedCount,
                'logs' => $logs,
            ]
        ]);
    }
    private function personalizeMessage($message, Contact $contact)
    {
        $replacements = [
            '[NAMA]' => $contact->name,
            '[USERNAME]' => $contact->username,
            '[PANGGILAN]' => $contact->greeting ?: $contact->name,
            '[NEGARA]' => $contact->country,
            '[KODE_NEGARA]' => $contact->country_code,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }
}
