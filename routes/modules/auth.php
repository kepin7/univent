<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// == GRUP UNTUK TAMU (GUEST / BELUM LOGIN) ==
Route::middleware('guest')->group(function () {
    // Tampilkan Form
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');

    // Aksi Form (POST)
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'loginWithEmail'])->name('login.post');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Google Auth
    Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

// == GRUP UNTUK VERIFIKASI OTP ==
// Ini juga 'guest' karena user belum ter-autentikasi penuh
Route::prefix('email')->middleware('guest')->group(function () {
    Route::get('/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice'); // Halaman form OTP
    Route::post('/verify', [AuthController::class, 'verifyOtp'])->name('verification.verify');       // Aksi verifikasi OTP
    Route::post('/resend', [AuthController::class, 'resendOtp'])->name('verification.resend');       // Aksi kirim ulang OTP
});

// == GRUP UNTUK USER YANG SUDAH LOGIN ==
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
