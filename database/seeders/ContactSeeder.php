<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\InvitationMessage;
use Illuminate\Support\Facades\Hash;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buat admin jika belum ada
        if (Admin::count() === 0) {
            // Buat admin mempelai pria
            $groomAdmin = Admin::create([
                'name' => 'Mempelai Pria',
                'email' => 'groom@example.com',
                'password' => Hash::make('password'),
                'role' => 'groom',
                'whatsapp_number' => '628123456789',
                'whatsapp_api_key' => 'example_api_key_1',
            ]);

            // Buat admin mempelai wanita
            $brideAdmin = Admin::create([
                'name' => 'Mempelai Wanita',
                'email' => 'bride@example.com',
                'password' => Hash::make('password'),
                'role' => 'bride',
                'whatsapp_number' => '628123456790',
                'whatsapp_api_key' => 'example_api_key_2',
            ]);
        } else {
            $groomAdmin = Admin::where('role', 'groom')->first();
            $brideAdmin = Admin::where('role', 'bride')->first();
        }

        // Daftar nama untuk kontak
        $groomContacts = [
            [
                'name' => 'Ahmad Rizky',
                'phone_number' => '6281234567801',
                'greeting' => 'Pak Ahmad',
                'invitation_status' => 'terkirim',
                'message' => 'Selamat menempuh hidup baru! Semoga menjadi keluarga yang sakinah, mawaddah, warahmah.',
                'attendance' => 'hadir'
            ],
            [
                'name' => 'Budi Santoso',
                'phone_number' => '6281234567802',
                'greeting' => 'Pak Budi',
                'invitation_status' => 'terkirim',
                'message' => 'Mohon maaf saya tidak bisa hadir. Semoga bahagia selalu untuk kedua mempelai.',
                'attendance' => 'tidak_hadir'
            ],
            [
                'name' => 'Candra Wijaya',
                'phone_number' => '6281234567803',
                'greeting' => 'Mas Candra',
                'invitation_status' => 'terkirim',
                'message' => 'Selamat berbahagia! Kita bertemu di hari H ya.',
                'attendance' => 'hadir'
            ],
            [
                'name' => 'Denny Mahendra',
                'phone_number' => '6281234567804',
                'greeting' => 'Mas Denny',
                'invitation_status' => 'belum_dikirim',
                'message' => null,
                'attendance' => null
            ],
            [
                'name' => 'Eko Prasetyo',
                'phone_number' => '6281234567805',
                'greeting' => 'Mas Eko',
                'invitation_status' => 'gagal',
                'message' => null,
                'attendance' => null
            ],
            [
                'name' => 'Faisal Rahman',
                'phone_number' => '6281234567806',
                'greeting' => 'Mas Faisal',
                'invitation_status' => 'belum_dikirim',
                'message' => null,
                'attendance' => null
            ],
            [
                'name' => 'Galih Pratama',
                'phone_number' => '6281234567807',
                'greeting' => 'Mas Galih',
                'invitation_status' => 'terkirim',
                'message' => 'Alhamdulillah... Semoga kalian selalu diberikan kebahagiaan dan keberkahan dalam rumah tangga.',
                'attendance' => 'belum_pasti'
            ],
        ];

        $brideContacts = [
            [
                'name' => 'Anisa Sari',
                'phone_number' => '6281234567811',
                'greeting' => 'Mbak Anisa',
                'invitation_status' => 'terkirim',
                'message' => 'Selamat menempuh hidup baru ya! Kita ketemu di acaranya!',
                'attendance' => 'hadir'
            ],
            [
                'name' => 'Bella Safitri',
                'phone_number' => '6281234567812',
                'greeting' => 'Mbak Bella',
                'invitation_status' => 'terkirim',
                'message' => 'Maaf tidak bisa hadir, semoga lancar acaranya ya...',
                'attendance' => 'tidak_hadir'
            ],
            [
                'name' => 'Cindy Rahayu',
                'phone_number' => '6281234567813',
                'greeting' => 'Mbak Cindy',
                'invitation_status' => 'terkirim',
                'message' => 'Barakallah untuk pernikahan kalian! Mau konfirmasi lagi untuk kehadiran, InsyaAllah akan hadir.',
                'attendance' => 'hadir'
            ],
            [
                'name' => 'Dewi Lestari',
                'phone_number' => '6281234567814',
                'greeting' => 'Mbak Dewi',
                'invitation_status' => 'belum_dikirim',
                'message' => null,
                'attendance' => null
            ],
            [
                'name' => 'Erni Susanti',
                'phone_number' => '6281234567815',
                'greeting' => 'Bu Erni',
                'invitation_status' => 'gagal',
                'message' => null,
                'attendance' => null
            ],
            [
                'name' => 'Fitri Handayani',
                'phone_number' => '6281234567816',
                'greeting' => 'Mbak Fitri',
                'invitation_status' => 'belum_dikirim',
                'message' => null,
                'attendance' => null
            ],
            [
                'name' => 'Gita Purnama',
                'phone_number' => '6281234567817',
                'greeting' => 'Mbak Gita',
                'invitation_status' => 'terkirim',
                'message' => 'Selamat ya, semoga menjadi keluarga yang sakinah, mawaddah, warahmah. Insya Allah saya hadir.',
                'attendance' => 'hadir'
            ],
        ];

        // Seed kontak mempelai pria
        $this->seedContacts($groomContacts, $groomAdmin);

        // Seed kontak mempelai wanita
        $this->seedContacts($brideContacts, $brideAdmin);

        // Tambahkan beberapa pesan ucapan yang belum disetujui
        $unapprovedMessages = [
            [
                'contact_name' => 'Ahmad Rizky',
                'name' => 'Risa Anggraini',
                'message' => 'Selamat untuk pernikahan kalian! Semoga menjadi keluarga yang bahagia selamanya.',
                'attendance' => 'belum_pasti',
                'is_approved' => false
            ],
            [
                'contact_name' => 'Anisa Sari',
                'name' => 'Dadang Supriyadi',
                'message' => 'Barakallah buat pernikahan kalian! Mohon maaf saya tidak bisa hadir.',
                'attendance' => 'tidak_hadir',
                'is_approved' => false
            ]
        ];

        foreach ($unapprovedMessages as $messageData) {
            $contact = Contact::where('name', $messageData['contact_name'])->first();

            if ($contact) {
                InvitationMessage::create([
                    'contact_id' => $contact->id,
                    'name' => $messageData['name'],
                    'message' => $messageData['message'],
                    'attendance' => $messageData['attendance'],
                    'is_approved' => $messageData['is_approved']
                ]);
            }
        }

        $this->command->info('Berhasil menambahkan ' .
            Contact::count() . ' kontak dan ' .
            InvitationMessage::count() . ' pesan ucapan.');
    }

    /**
     * Seed kontak untuk admin tertentu
     *
     * @param array $contacts Data kontak
     * @param Admin $admin Admin yang memiliki kontak
     * @return void
     */
    private function seedContacts($contacts, $admin)
    {
        foreach ($contacts as $contactData) {
            // Buat kontak
            $contact = Contact::create([
                'admin_id' => $admin->id,
                'name' => $contactData['name'],
                'phone_number' => $contactData['phone_number'],
                'country' => 'ID',
                'country_code' => '62',
                'greeting' => $contactData['greeting'],
                'invitation_status' => $contactData['invitation_status'],
                'sent_at' => $contactData['invitation_status'] === 'terkirim' ? now() : null,
            ]);

            // Jika ada pesan, tambahkan pesan ucapan
            if (!is_null($contactData['message'])) {
                InvitationMessage::create([
                    'contact_id' => $contact->id,
                    'name' => $contactData['name'],
                    'message' => $contactData['message'],
                    'attendance' => $contactData['attendance'],
                    'is_approved' => true
                ]);
            }
        }
    }
}
