<?php

namespace App\Services; // Namespace service OTP

use App\Mail\SendOtpMail; // Mail OTP
use App\Models\User; // Model User
use Illuminate\Support\Facades\Mail; // Facade Mail
use Exception; // Exception

class OtpService
{
    // Method untuk generate OTP dan langsung mengirimkannya ke email user
    public function generateAndSend(User $user, int $minutes = 5): void
    {
        // Generate OTP acak 6 digit dan konversi ke string
        $otp = (string) rand(100000, 999999);

        // Menyimpan OTP ke kolom otp_code milik user
        $user->otp_code = $otp;

        // Menentukan waktu kadaluarsa OTP (default 5 menit)
        $user->otp_expires_at = now()->addMinutes($minutes);

        // Menyimpan perubahan ke database
        $user->save();

        // Mengirim email OTP ke alamat email user
        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    // Method untuk mengirim ulang OTP dengan batas percobaan
    public function resendOtp(User $user, int $minutes = 5): void
    {
        // Mengecek apakah user sudah melebihi batas resend OTP
        if ($user->resend_attempts >= 3) {
            // Jika melebihi, lempar exception
            throw new Exception('Batas maksimal terlampaui. Silakan coba lagi nanti.');
        }

        // Generate OTP baru secara acak
        $otp = (string) rand(100000, 999999);

        // Mengganti OTP lama dengan OTP baru
        $user->otp_code = $otp;

        // Mengatur ulang waktu kadaluarsa OTP
        $user->otp_expires_at = now()->addMinutes($minutes);

        // Menambah jumlah percobaan resend OTP
        $user->resend_attempts += 1;

        // Menyimpan semua perubahan ke database
        $user->save();

        // Mengirim email OTP yang baru
        Mail::to($user->email)->send(new SendOtpMail($otp));
    }

    // Method untuk memverifikasi OTP yang dimasukkan user
    public function verify(User $user, string $otp): bool
    {
        // Jika user belum memiliki OTP atau waktu kadaluarsa belum diset
        if (! $user->otp_code || ! $user->otp_expires_at) {
            return false; // OTP belum dibuat
        }

        // Mengecek apakah OTP sudah melewati waktu kadaluarsa
        if (now()->gt($user->otp_expires_at)) {
            return false; // OTP sudah kadaluarsa
        }

        // Membandingkan OTP di database dengan OTP input user secara ketat
        if ((string) $user->otp_code !== $otp) {
            return false; // OTP tidak cocok
        }

        // Jika semua pengecekan lolos, OTP valid
        return true;
    }

    // Method untuk mereset data OTP setelah berhasil diverifikasi
    public function reset(User $user): void
    {
        // Menghapus kode OTP dari database
        $user->otp_code = null;

        // Menghapus waktu kadaluarsa OTP
        $user->otp_expires_at = null;

        // Mengatur ulang jumlah percobaan resend OTP ke 0
        $user->resend_attempts = 0;

        // Menyimpan perubahan ke database
        $user->save();
    }
}