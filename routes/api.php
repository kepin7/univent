<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FeedbackController;

//authentication
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/login-email', [AuthController::class, 'loginWithEmail']);

//verifikasi otp
Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp']);

//resend otp
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);

// Password Reset
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// Google Auth
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/auth/google/logout', [AuthController::class, 'logoutGoogle'])->middleware('auth:sanctum');

// Protected (butuh login Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me', fn (Request $request) => $request->user());
});

Route::middleware(['auth:sanctum', 'checkTokenExpiry', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->middleware('admin');
});

Route::middleware(['auth:sanctum', 'checkTokenExpiry'])->group(function () {
    Route::get('/user/dashboard', [UserController::class, 'dashboard']);
});

Route::post('/feedback', [FeedbackController::class, 'store']);