<?php

namespace App\Http\Controllers; // Namespace controller

use App\Models\User; // Model User
use App\Services\AuthService; // Service autentikasi
use Illuminate\Http\Request; // HTTP request
use Illuminate\Support\Facades\Auth; // Auth facade
use Illuminate\Support\Facades\Hash; // Hash password
use Illuminate\View\View; // Return view
use Illuminate\Http\RedirectResponse; // Return redirect

class AuthController extends Controller
{
    // Service autentikasi (Dependency Injection)
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService; // Inject AuthService
    }

    // =========================
    // TAMPILAN HALAMAN (VIEW)
    // =========================

    public function showRegisterForm(): View
    {
        return view('auth.register'); // Form register
    }

    public function showLoginForm(): View
    {
        return view('auth.login'); // Form login
    }

    public function showVerificationNotice(Request $request): View
    {
        // Tampilkan halaman verifikasi email
        return view('auth.email-verification', [
            'email' => $request->query('email') // Ambil email dari query string
        ]);
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password'); // Form lupa password
    }

    public function showResetPasswordForm(Request $request): View
    {
        // Form reset password + email dari URL
        return view('auth.reset-password', [
            'email' => $request->query('email')
        ]);
    }

    // =========================
    // AKSI AUTENTIKASI
    // =========================

    public function register(Request $request): RedirectResponse
    {
        // Validasi input register
        $request->validate([
            'email' => 'required|email|unique:users,email', // Email unik
            'password' => 'required|min:8|confirmed', // Password + konfirmasi
        ]);

        return $this->authService->register($request); // Proses via service
    }

    public function loginWithEmail(Request $request): RedirectResponse
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email', // Email wajib
            'password' => 'required', // Password wajib
        ]);

        /** @var \App\Models\User|null $user */
        $user = User::where('email', $request->email)->first(); // Cari user

        // Cek user & kecocokan password
        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password ?? '')) {
            return back()->withErrors(['login' => 'Email atau password salah']);
        }

        // =========================
        // LOGIKA SKIP OTP
        // =========================

        // Cek apakah perangkat sudah dipercaya
        $isTrusted = $request->cookie('device_trusted_' . $user->id);

        if ($isTrusted === 'true' || $user->isAdmin()) {
            // Admin / device terpercaya → login langsung
            Auth::login($user, $request->has('remember')); // Login user
            $request->session()->regenerate(); // Regenerate session

            return $user->isAdmin()
                ? redirect()->route('dashboard') // Dashboard admin
                : redirect()->route('dashboard'); // Dashboard user
        }

        // User biasa + device baru → lanjut OTP
        return $this->authService->loginWithEmail($request);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        // Validasi OTP
        $request->validate([
            'email' => 'required|email', // Email wajib
            'otp' => 'required|string|size:6', // OTP 6 digit
        ]);

        return $this->authService->verifyOtp($request); // Verifikasi OTP
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email' // Validasi email
        ]);

        return $this->authService->forgotPassword($request); // Kirim OTP reset
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        // Validasi reset password
        $request->validate([
            'email' => 'required|email', // Email
            'otp' => 'required|string|size:6', // OTP
            'password' => 'required|min:8|confirmed', // Password baru
        ]);

        return $this->authService->resetPassword($request); // Reset password
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email' // Validasi email
        ]);

        return $this->authService->resendOtp($request); // Kirim ulang OTP
    }

    public function logout(Request $request): RedirectResponse
    {
        return $this->authService->logout($request); // Logout user
    }

    // =========================
    // GOOGLE AUTH
    // =========================

    public function redirectToGoogle(): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        // Redirect ke halaman login Google
        return $this->authService->redirectToGoogle();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        // Handle callback dari Google
        return $this->authService->handleGoogleCallback($request);
    }
}