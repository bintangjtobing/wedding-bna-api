<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function sendMessage($apiKey, $phoneNumber, $message)
    {
        try {
            // Implementasi untuk mengirim pesan WhatsApp
            // Sesuaikan dengan provider WhatsApp API yang digunakan
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
}
