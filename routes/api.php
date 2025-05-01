<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageTemplateController;
// API routes yang memerlukan autentikasi
Route::middleware('auth:api')->group(function () {
    // API untuk kontak
    Route::get('/contacts', [ContactController::class, 'apiGetContacts']);
    Route::post('/contacts', [ContactController::class, 'apiAddContact']);

    // API untuk pesan
    Route::post('/messages/send', [MessageController::class, 'apiSendMessage']);
    Route::get('/statistics', [App\Http\Controllers\Api\StatisticController::class, 'getStatistics']);
    Route::post('/messages/resend-failed', [MessageController::class, 'apiResendFailed']);
    Route::get('/contacts/export', [ContactController::class, 'apiExportContacts']);
    Route::get('/contacts/search', [ContactController::class, 'apiSearchContacts']);
    Route::post('/contacts/bulk-delete', [ContactController::class, 'apiBulkDelete']);
    Route::get('/templates', [MessageTemplateController::class, 'apiGetTemplates']);
    Route::post('/templates', [MessageTemplateController::class, 'apiStoreTemplate']);
    Route::put('/templates/{template}', [MessageTemplateController::class, 'apiUpdateTemplate']);
    Route::delete('/templates/{template}', [MessageTemplateController::class, 'apiDeleteTemplate']);
});