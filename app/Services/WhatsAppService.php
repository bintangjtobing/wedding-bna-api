<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Mengirim pesan WhatsApp menggunakan Fonnte API
     *
     * @param  string  $apiKey        API key dari Fonnte
     * @param  string  $phoneNumber   Nomor telepon penerima
     * @param  string  $message       Isi pesan
     * @param  string  $recipientName Nama penerima (opsional)
     * @return array
     */
    public function sendMessage($apiKey, $phoneNumber, $message, $recipientName = null)
    {
        try {
            // Persiapan nomor telepon
            // Hapus karakter '+' jika ada di depan nomor
            $phoneNumber = ltrim($phoneNumber, '+');

            // Format target untuk Fonnte
            $target = $phoneNumber;
            if ($recipientName) {
                $target .= '|' . $recipientName;
            }

            // Inisialisasi cURL
            $curl = curl_init();

            // Set opsi cURL
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62', // Kode negara Indonesia
                    'typing' => false,
                    'delay' => '1',
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $apiKey
                ],
            ]);

            // Eksekusi cURL
            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Tutup koneksi cURL
            curl_close($curl);

            // Log respons untuk debugging
            Log::info('Fonnte API Response', [
                'phone' => $phoneNumber,
                'response' => $response,
                'http_code' => $httpcode
            ]);

            // Handle error
            if ($err) {
                return [
                    'success' => false,
                    'error' => $err,
                ];
            }

            // Parse JSON response
            $responseData = json_decode($response, true);

            // Cek status dari respons API
            if (isset($responseData['status']) && $responseData['status'] === true) {
                return [
                    'success' => true,
                    'response' => $responseData,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseData['reason'] ?? 'Unknown error',
                ];
            }

        } catch (\Exception $e) {
            // Log error
            Log::error('Error sending WhatsApp message', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mengirim pesan WhatsApp dengan gambar menggunakan Fonnte API
     *
     * @param  string  $apiKey        API key dari Fonnte
     * @param  string  $phoneNumber   Nomor telepon penerima
     * @param  string  $message       Isi pesan
     * @param  string  $imageUrl      URL gambar yang akan dikirim
     * @param  string  $recipientName Nama penerima (opsional)
     * @return array
     */
    public function sendImageMessage($apiKey, $phoneNumber, $message, $imageUrl, $recipientName = null)
    {
        try {
            // Persiapan nomor telepon
            $phoneNumber = ltrim($phoneNumber, '+');

            // Format target untuk Fonnte
            $target = $phoneNumber;
            if ($recipientName) {
                $target .= '|' . $recipientName;
            }

            // Inisialisasi cURL
            $curl = curl_init();

            // Set opsi cURL
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'target' => $target,
                    'message' => $message,
                    'url' => $imageUrl, // URL gambar
                    'countryCode' => '62',
                    'typing' => false,
                    'delay' => '1',
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $apiKey
                ],
            ]);

            // Eksekusi cURL
            $response = curl_exec($curl);
            $err = curl_error($curl);

            // Tutup koneksi cURL
            curl_close($curl);

            // Handle error
            if ($err) {
                return [
                    'success' => false,
                    'error' => $err,
                ];
            }

            // Parse JSON response
            $responseData = json_decode($response, true);

            // Cek status dari respons API
            if (isset($responseData['status']) && $responseData['status'] === true) {
                return [
                    'success' => true,
                    'response' => $responseData,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseData['reason'] ?? 'Unknown error',
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mengirim pesan WhatsApp massal ke beberapa penerima menggunakan Fonnte API
     *
     * @param  string  $apiKey    API key dari Fonnte
     * @param  array   $contacts  Array kontak (phoneNumber dan name)
     * @param  string  $message   Isi pesan
     * @return array
     */
    public function sendBulkMessage($apiKey, $contacts, $message)
    {
        try {
            // Format target untuk Fonnte
            $targets = [];
            foreach ($contacts as $contact) {
                $phone = ltrim($contact['phone_number'], '+');
                if (isset($contact['name'])) {
                    $targets[] = $phone . '|' . $contact['name'];
                } else {
                    $targets[] = $phone;
                }
            }

            $targetString = implode(',', $targets);

            // Inisialisasi cURL
            $curl = curl_init();

            // Set opsi cURL
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60, // Waktu timeout lebih lama untuk bulk message
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'target' => $targetString,
                    'message' => $message,
                    'countryCode' => '62',
                    'typing' => false,
                    'delay' => '1',
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $apiKey
                ],
            ]);

            // Eksekusi cURL
            $response = curl_exec($curl);
            $err = curl_error($curl);

            // Tutup koneksi cURL
            curl_close($curl);

            // Handle error
            if ($err) {
                return [
                    'success' => false,
                    'error' => $err,
                ];
            }

            // Parse JSON response
            $responseData = json_decode($response, true);

            // Cek status dari respons API
            if (isset($responseData['status']) && $responseData['status'] === true) {
                return [
                    'success' => true,
                    'response' => $responseData,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseData['reason'] ?? 'Unknown error',
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
