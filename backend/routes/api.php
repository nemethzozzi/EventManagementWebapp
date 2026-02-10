<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
// Translations (public)
Route::post('/password/forgot', [PasswordResetController::class, 'forgotPassword'])
    ->middleware('throttle:5,1'); // Rate limit: 5 kérés / perc
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword']);
Route::get('/password/reset/{token}', function (string $token, Request $request) {
    $email = $request->query('email', '');
    $frontend = rtrim(env('FRONTEND_URL', 'http://localhost:5173'), '/');

    return redirect()->away(
        $frontend.'/reset-password/'.$token.'?email='.urlencode($email)
    );
})->name('password.reset');

// Protected routes (JWT required)
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Events (minden bejelentkezett user)
    Route::apiResource('events', EventController::class);

    // Chat (user)
    Route::post('/chat/message', [ChatController::class, 'sendMessage'])
        ->middleware('throttle:30,1'); // Rate limit: 30 üzenet / perc
    Route::get('/chat/conversation', [ChatController::class, 'getConversation']);

    // Chat (helpdesk_agent)
    Route::middleware('role:helpdesk_agent,admin')->group(function () {
        Route::get('/chat/conversations', [ChatController::class, 'listConversations']);
        Route::get('/chat/conversations/{id}', [ChatController::class, 'getConversationById']);
        Route::post('/chat/conversations/{id}/message', [ChatController::class, 'sendAgentMessage'])
            ->middleware('throttle:30,1');
        Route::post('/chat/conversations/{id}/close', [ChatController::class, 'closeConversation']);
    });

    // Admin (csak admin role)
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'listUsers']);
        Route::post('/admin/users', [AdminController::class, 'createUser']);
        Route::put('/admin/users/{id}/role', [AdminController::class, 'updateUserRole']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    });
});
