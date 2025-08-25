<?php

use App\Http\Controllers\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], static function () {
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');
    
    // OTP routes
    Route::post('generate-otp', [AuthController::class, 'generateOtp'])->name('generate-otp');
    Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
    
    Route::middleware(['auth:api'])->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::put('me', [AuthController::class, 'updateMe'])->name('updateMe');
        Route::get('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});