<?php

namespace App\Services;

use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class OtpService
{
    public function generateAndSend(User $user)
    {
        $otp = rand(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    public function verify(User $user, $otp)
    {
        if (!$user->otp_code || !$user->otp_expires_at) {
            return false; // belum ada otp
        }

        if (now()->gt($user->otp_expires_at)) {
            return false; // kadaluarsa
        }

        if ($user->otp_code !== $otp) {
            return false; // salah
        }

        return true;
    }

    public function reset(User $user)
    {
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
    }
}