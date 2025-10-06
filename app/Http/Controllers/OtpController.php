<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Cache;

class OtpController extends Controller
{
    // Kirim OTP ke email
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $otp = rand(100000, 999999); // generate 6 digit OTP

        // Simpan OTP ke cache sementara (5 menit)
        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

        // Kirim email OTP
        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'Kode OTP sudah dikirim ke email Anda'
        ]);
    }

    // Verifikasi OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        $cachedOtp = Cache::get('otp_' . $request->email);

        if (!$cachedOtp) {
            return response()->json(['message' => 'OTP kadaluarsa'], 400);
        }

        if ($request->otp != $cachedOtp) {
            return response()->json(['message' => 'OTP salah'], 400);
        }

        // OTP benar maka akan hapus OTP dari cache
        Cache::forget('otp_' . $request->email);

        return response()->json(['message' => 'OTP valid']);
    }
}