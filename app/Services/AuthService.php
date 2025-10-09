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
            'is_active'=> false,
        ]);

        $user->assignRole('user');

        // kirim OTP
        $this->otpService->generateAndSend($user);

        return response()->json([
            'message' => 'Akun berhasil dibuat. Silakan cek email untuk kode OTP.',
            'email'   => $user->email,
        ], 201);
    }

    //login email
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

    ///verikasi otp
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if (!$this->otpService->verify($user, $request->otp)) {
            return response()->json(['message' => 'OTP tidak valid atau sudah kadaluarsa'], 400);
        }

        // OTP valid -> reset OTP & aktifkan user
        $this->otpService->reset($user);

        $user->email_verified_at = now();
        $user->is_active = true;
        $user->save();

        // Buat token Sanctum valid 2 jam
        $token = $user->createToken('API Token', [], now()->addHours(2))->plainTextToken;

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

    //login google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Cari user di DB berdasarkan email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Buat user baru
                $user = User::create([
                    'name'              => $googleUser->getName(),
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'password'          => bcrypt(str()->random(16)),
                    'email_verified_at' => now(),
                    'is_active'         => true,
                ]);
                $user->assignRole('user');
            } else {
                // Update data user lama
                $user->google_id = $googleUser->getId();   
                $user->avatar    = $googleUser->getAvatar();
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }
                $user->is_active = true;
                $user->save();
            }

            // Buat token Sanctum
            $token = $user->createToken('API Token')->plainTextToken;

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

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Login Google gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    
    //logout google
    public function logoutGoogle(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil, token dihapus',
        ]);
    }

    /** FORGOT PASSWORD */
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }

        $otp = rand(100000, 999999);

        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::raw("Kode reset password Anda adalah: $otp", function ($message) use ($user) {
            $message->to($user->email)->subject('Reset Password');
        });

        return response()->json(['message' => 'Kode reset password dikirim ke email Anda']);
    }

    //reset password
    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->otp_code !== $request->otp || now()->gt($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP tidak valid atau kadaluarsa'], 400);
        }

        $user->password = bcrypt($request->password);
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password berhasil direset']);
    }

    //resend otp
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

    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil, token dihapus',
        ]);
    }
}