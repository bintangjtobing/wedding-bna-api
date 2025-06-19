<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageTemplateController;
use App\Http\Controllers\Api\InvitationMessageController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route Public tanpa autentikasi
Route::post('/login', [AuthController::class, 'login']);
Route::get('/invitation/{username}', [App\Http\Controllers\ContactController::class, 'apiGetContactByUsername']);
Route::post('/invitation/messages', [App\Http\Controllers\Api\InvitationMessageController::class, 'store']);
Route::get('/invitation/messages/all', function() {
    $messages = \App\Models\InvitationMessage::where('is_approved', true)
        ->orderBy('created_at', 'desc')
        ->with('contact:id,name,username')
        ->get();

    return response()->json([
        'status' => 'success',
        'data' => $messages
    ]);
});
Route::post('/test-broadcast', [InvitationMessageController::class, 'testBroadcast']);
Route::get('/invitation/{username}/messages', [App\Http\Controllers\Api\InvitationMessageController::class, 'getMessagesByUsername']);
Route::get('/test/device-detection', [App\Http\Controllers\Api\ClickLogController::class, 'testDeviceDetection']);
Route::post('/test/device-detection', [App\Http\Controllers\Api\ClickLogController::class, 'testDeviceDetection']);


// Route yang memerlukan autentikasi
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    // Kontak
    Route::get('/contacts', [ContactController::class, 'apiGetContacts']);
    Route::get('/contacts/search', [ContactController::class, 'apiSearchContacts']);
    Route::get('/contacts/failed', [ContactController::class, 'apiGetFailedContacts']);
    Route::get('/contacts/export', [ContactController::class, 'apiExportContacts']);
    Route::post('/contacts', [ContactController::class, 'apiAddContact']);
    Route::post('/contacts/import', [ContactController::class, 'apiImportContacts']);
    Route::post('/contacts/bulk-delete', [ContactController::class, 'apiBulkDelete']);
    Route::patch('/contacts/{contact}/reset-status', [ContactController::class, 'apiResetStatus']);
    Route::post('/contacts/reset-all', [ContactController::class, 'apiResetAllStatus']);
    Route::put('/contacts/{contact}', [ContactController::class, 'apiUpdateContact']);
    Route::delete('/contacts/{contact}', [ContactController::class, 'apiDeleteContact']);

    // Pesan
    Route::post('/messages/send', [MessageController::class, 'apiSendMessage']);
    Route::post('/messages/resend-failed', [MessageController::class, 'apiResendFailed']);

    // Template Pesan
    Route::get('/templates', [MessageTemplateController::class, 'apiGetTemplates']);
    Route::post('/templates', [MessageTemplateController::class, 'apiStoreTemplate']);
    Route::put('/templates/{template}', [MessageTemplateController::class, 'apiUpdateTemplate']);
    Route::delete('/templates/{template}', [MessageTemplateController::class, 'apiDeleteTemplate']);

    // Statistik
    Route::get('/statistics', [StatisticController::class, 'getStatistics']);

    Route::patch('/invitation/messages/{message}', [App\Http\Controllers\Api\InvitationMessageController::class, 'updateApprovalStatus']);
    Route::delete('/invitation/messages/{message}', [App\Http\Controllers\Api\InvitationMessageController::class, 'destroy']);
    Route::prefix('analytics')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\ClickLogController::class, 'getOverallStats']);
        Route::get('/contact/{contactId}/clicks', [App\Http\Controllers\Api\ClickLogController::class, 'getContactClickLogs']);
        Route::get('/recent-activities', [App\Http\Controllers\Api\ClickLogController::class, 'getRecentActivities']);
        Route::get('/date-range', [App\Http\Controllers\Api\ClickLogController::class, 'getAnalyticsByDateRange']);
    });
});