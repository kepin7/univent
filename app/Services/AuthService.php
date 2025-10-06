<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthService
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }
    
    //register
    public function register(Request $request)
    {
        $user = User::create([
            'name'     => explode('@', $request->email)[0],
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'user',
        ]);

        $user->assignRole('user');

        // gunakan OtpService
        $this->otpService->generateAndSend($user);

        return response()->json([
            'message' => 'Akun berhasil dibuat. Silakan cek email untuk kode OTP.',
            'email'   => $user->email,
        ], 201);
    }

    //Login email
    public function loginWithEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        // kirim OTP baru
        $this->otpService->generateAndSend($user);

        return response()->json([
            'message' => 'Kode OTP telah dikirim ke email Anda',
            'email'   => $user->email,
        ]);
    }

    //Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if (!$this->otpService->verify($user, $request->otp)) {
            return response()->json(['message' => 'OTP salah atau kadaluarsa'], 400);
        }

        // Reset setelah valid
        $this->otpService->reset($user);

        // Set email verified
        $user->update(['email_verified_at' => now()]);

        // Buat token
        $tokenResult = $user->createToken('auth_token', ['*']);
        $token = $tokenResult->plainTextToken;

        $user->tokens()->where('id', explode('|', $token)[0])->update([
            'expires_at' => now()->addHours(2)
        ]);

        return response()->json([
            'message'    => 'Login berhasil',
            'token'      => $token,
            'expires_at' => now()->addHours(2)->toDateTimeString(),
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }

    //Login Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();

    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Cek apakah email sudah ada
        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'      => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'password'  => bcrypt(str()->random(16)), // jaga-jaga biar tidak null
            ]
        );

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    /** FORGOT PASSWORD */
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        $token = rand(100000, 999999);
        $user->update([
            'reset_token' => $token,
            'reset_expires_at' => now()->addMinutes(10),
        ]);

        Mail::raw("Kode reset password Anda adalah: $token", function ($message) use ($user) {
            $message->to($user->email)->subject('Reset Password');
        });

        return response()->json(['message' => 'Kode reset password dikirim ke email Anda']);
    }

    /** RESET PASSWORD */
    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->reset_token !== $request->token || now()->gt($user->reset_expires_at)) {
            return response()->json(['message' => 'Token tidak valid atau kadaluarsa'], 400);
        }

        $user->update([
            'password' => bcrypt($request->password),
            'reset_token' => null,
            'reset_expires_at' => null,
        ]);

        return response()->json(['message' => 'Password berhasil direset']);
    }

    /** RESEND OTP */
    public function resendOtp(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $this->otpService->generateAndSend($user);

        return response()->json([
            'message' => 'Kode OTP baru telah dikirim ke email Anda',
            'email'   => $user->email,
        ]);
    }

    /** LOGOUT */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil, token dihapus',
        ]);
    }
}