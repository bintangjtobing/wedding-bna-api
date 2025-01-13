<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Http\Request;
use App\Models\Guest;
use App\Helper\SendMessage;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function(){
    return view('dashboard.pages.index');
})->name('dashboard');
Route::get('/users', function(){
    return view('dashboard.pages.users.index');
})->name('users');

Route::get('/guests', function () {
    $guests = Guest::all();
    return view('dashboard.pages.guests.index', ['guests' => $guests]);
    // return response()->json($guests);
});

Route::post('/send-invitations', function () {
    $guests = Guest::all();
    $sendMessage = new SendMessage();

    foreach ($guests as $guest) {
        $specificCall = $guest->specific_call ?? 'Kak';
        $slug = strtolower(str_replace(' ', '-', $guest->name));

        $message = "Halo {$specificCall} {$guest->name},\n\n";
        $message .= "Tanpa mengurangi rasa hormat, izinkan kami mengundang {$specificCall} {$guest->name} untuk menghadiri acara pernikahan kami.\n\n";
        $message .= "*Berikut link undangan kami*, untuk info lengkap dari acara bisa kunjungi:\n";
        $message .= "https://wedding-bintang.baharihari.com/mengundang/{$slug}\n\n";
        $message .= "Merupakan suatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk menyempatkan waktu hadir dan memberikan doa restu ke acara yang telah kami sediakan.\n\n";
        $message .= "*Mohon maaf perihal undangan hanya dibagikan melalui pesan Whatsapp.*\n\n";
        $message .= "Diharapkan untuk *tetap menjaga kesehatan bersama dan sangat besar harapan untuk datang pada jam yang telah disepakati.*\n\n";
        $message .= "Terima kasih banyak atas perhatiannya.\n\n";
        $message .= "Kami yang mengundang dengan bahagia,\n";
        $message .= "Bintang Tobing & Ayu Sinaga";

        $sendMessage->send($guest->phone_number, $message);
    }

    return redirect()->back()->with('success', 'Invitations successfully sent!');
});
Route::post('/guests', function (Request $request) {
    // Validasi input
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'specific_call' => 'required|string',
        'email' => 'required|email|max:255',
        'phone_number' => 'required|string|max:20',
        'friend_of' => 'nullable|string',
        'region' => 'nullable|string',
        'gender' => 'nullable|string',
        'attend' => 'required|integer|in:0,1,2',
    ]);

    $validated['slug_name'] = strtolower(str_replace(' ', '-', $validated['name']));

    Guest::create($validated);

    return redirect()->back()->with('success', 'Tamu berhasil ditambahkan');
});