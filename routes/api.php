<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API routes yang memerlukan autentikasi
Route::middleware('auth:api')->group(function () {
    // API untuk kontak
    Route::get('/contacts', [ContactController::class, 'apiGetContacts']);
    Route::post('/contacts', [ContactController::class, 'apiAddContact']);

    // API untuk pesan
    Route::post('/messages/send', [MessageController::class, 'apiSendMessage']);
    Route::get('/statistics', [App\Http\Controllers\Api\StatisticController::class, 'getStatistics']);
});