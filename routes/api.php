<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\InvitationController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/mengundang', [InvitationController::class, 'create']);
Route::get('/mengundang', [InvitationController::class, 'index']);
Route::get('/mengundang/{slug}', [InvitationController::class, 'show']);
Route::post('/mengundang/{slug}/reply', [InvitationController::class, 'replyToComment']);
Route::post('/mengundang/{slug}/attendance', [InvitationController::class, 'updateAttendance']);