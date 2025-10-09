<?php

namespace App\Services;

use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class OtpService
{
    public function generateAndSend(User $user, int $minutes = 5)
    {
        $otp = rand(100000, 999999);

        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes($minutes);
        $user->save();

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    public function verify(User $user, string $otp): bool
    {
        if (!$user->otp_code || !$user->otp_expires_at) {
            return false; // belum ada OTP
        }

        if (now()->gt($user->otp_expires_at)) {
            return false; // OTP kadaluarsa
        }

        if ($user->otp_code !== $otp) {
            return false; // OTP salah
        }

        return true;
    }

    public function reset(User $user)
    {
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();
    }
}