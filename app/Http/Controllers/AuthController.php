<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        return $this->authService->register($request);
    }

    public function loginWithEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        return $this->authService->loginWithEmail($request);
    }

    public function redirectToGoogle()
    {
        return $this->authService->redirectToGoogle();
    }

    public function handleGoogleCallback()
    {
        return $this->authService->handleGoogleCallback();
    }

    public function logoutGoogle(Request $request)
    {
        return $this->authService->logoutGoogle($request);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);
        return $this->authService->verifyOtp($request);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return $this->authService->forgotPassword($request);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'password' => 'required|min:6|confirmed'
        ]);
        return $this->authService->resetPassword($request);
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        return $this->authService->resendOtp($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
}