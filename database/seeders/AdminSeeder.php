<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Buat admin mempelai pria
        Admin::create([
            'name' => 'Mempelai Pria',
            'email' => 'groom@example.com',
            'password' => Hash::make('password'),
            'role' => 'groom',
            'whatsapp_number' => '+628123456789',
            'whatsapp_api_key' => 'groom_api_key_here',
        ]);

        // Buat admin mempelai wanita
        Admin::create([
            'name' => 'Mempelai Wanita',
            'email' => 'bride@example.com',
            'password' => Hash::make('password'),
            'role' => 'bride',
            'whatsapp_number' => '+628987654321',
            'whatsapp_api_key' => 'bride_api_key_here',
        ]);
    }
}
