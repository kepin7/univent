<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Cache;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $otp = rand(100000, 999999); 

        Cache::put('otp_' . $request->email, $otp, now()->addMinutes(5));

        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json([
            'message' => 'Kode OTP sudah dikirim ke email Anda'
        ]);
    }

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

        Cache::forget('otp_' . $request->email);

        return response()->json(['message' => 'OTP valid']);
    }
}