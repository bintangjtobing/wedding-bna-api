<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageTemplateController;

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
    Route::get('/messages/failed', [MessageController::class, 'showFailedContacts'])->name('messages.failed');
    Route::post('/messages/resend-failed', [MessageController::class, 'resendFailed'])->name('messages.resendFailed');
    Route::get('/contacts/import', [ContactController::class, 'showImportForm'])->name('contacts.import');
    Route::post('/contacts/import', [ContactController::class, 'importContacts'])->name('contacts.processImport');
    Route::get('/contacts/export', [ContactController::class, 'exportContacts'])->name('contacts.export');
    Route::get('/contacts/export-template', [ContactController::class, 'exportTemplate'])->name('contacts.exportTemplate');
    Route::post('/contacts/bulk-delete', [ContactController::class, 'bulkDelete'])->name('contacts.bulkDelete');
    Route::get('/templates', [MessageTemplateController::class, 'index'])->name('templates.index');
    Route::get('/templates/create', [MessageTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [MessageTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{template}/edit', [MessageTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{template}', [MessageTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{template}', [MessageTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::get('/templates/{template}/use', [MessageTemplateController::class, 'use'])->name('templates.use');
    Route::post('/contacts/reset-all', [ContactController::class, 'resetAllStatus'])->name('contacts.resetAll');
    Route::patch('/contacts/{contact}/reset-status', [ContactController::class, 'resetStatus'])->name('contacts.resetStatus');
    Route::get('/invitation-messages', [App\Http\Controllers\InvitationMessageController::class, 'index'])->name('invitation_messages.index');
    Route::patch('/invitation-messages/{message}/toggle-approval', [App\Http\Controllers\InvitationMessageController::class, 'toggleApproval'])->name('invitation_messages.toggle_approval');
    Route::delete('/invitation-messages/{message}', [App\Http\Controllers\InvitationMessageController::class, 'destroy'])->name('invitation_messages.destroy');
    Route::get('/laravel-websockets', function() {
        return view('websockets');
    });
});
