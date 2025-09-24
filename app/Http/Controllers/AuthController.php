<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => explode('@', $request->email)[0], // username dari email
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        // Kirim OTP ke email
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'Akun berhasil dibuat. Silakan cek email untuk kode OTP.',
            'email'   => $user->email,
        ], 201);
    }

    //Login pakai email
    public function loginWithEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        // Generate OTP baru
        $otp = rand(100000, 999999);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        // Kirim OTP via email
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'Kode OTP telah dikirim ke email Anda',
            'email'   => $user->email,
        ]);
    }

    //Verifikasi OTP.
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->otp_code !== $request->otp) {
            return response()->json(['message' => 'OTP salah'], 400);
        }

        if (now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP sudah kadaluarsa'], 400);
        }

        // Reset OTP
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->email_verified_at = now();
        $user->save();

        // Buat token Sanctum → otomatis login
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    //Redirect ke Google OAuth.
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Cek apakah email sudah ada
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Kalau belum ada akun, register otomatis
            $user = User::create([
                'name' => $googleUser->getName(),   // ← otomatis ambil "Kevin"
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(str()->random(16)), // random password, ga dipakai
                'google_id' => $googleUser->getId(),
            ]);
        }

        Auth::login($user);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        // Buat OTP lagi (untuk keamanan login)
        $otpCode = rand(100000, 999999);
        Otp::create([
            'email' => $user->email,
            'code' => $otpCode,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw("Kode OTP Login Anda adalah: $otpCode", function ($message) use ($user) {
            $message->to($user->email)->subject('Login OTP');
        });

        return response()->json([
            'status' => 'pending',
            'message' => 'Kode OTP login dikirim ke email.',
        ]);
    }
    //forgot password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        // Buat kode reset (6 digit)
        $token = rand(100000, 999999);
        $user->reset_token = $token;
        $user->reset_expires_at = now()->addMinutes(10);
        $user->save();

        // Kirim email kode reset
        Mail::raw("Kode reset password Anda adalah: $token", function ($message) use ($user) {
            $message->to($user->email)->subject('Reset Password');
        });

        return response()->json(['message' => 'Kode reset password dikirim ke email Anda']);
    }

    //reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Validasi token
        if ($user->reset_token !== $request->token || now()->gt($user->reset_expires_at)) {
            return response()->json(['message' => 'Token tidak valid atau kadaluarsa'], 400);
        }

        // Update password
        $user->password = bcrypt($request->password);
        $user->reset_token = null;
        $user->reset_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password berhasil direset']);
    }

    //Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }
}