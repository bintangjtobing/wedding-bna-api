<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Route untuk halaman publik
Route::get('/', function () {
    return view('welcome');
});

// Route untuk autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route yang memerlukan autentikasi
Route::middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route untuk kontak
    Route::resource('contacts', ContactController::class);

    // Route untuk pesan
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages/send', [MessageController::class, 'send'])->name('messages.send');
});
