<?php

namespace App\Mail; // Namespace untuk class email (Mailable)

use Illuminate\Bus\Queueable; // Trait agar email bisa dikirim via queue
use Illuminate\Mail\Mailable; // Base class untuk email Laravel
use Illuminate\Queue\SerializesModels; // Trait serialisasi data untuk queue

class SendOtpMail extends Mailable // Class email untuk OTP
{
    use Queueable, SerializesModels; // Aktifkan fitur queue & serialisasi

    // Properti untuk menyimpan kode OTP
    public string $otp;

    // Constructor menerima OTP sebagai string
    public function __construct(string $otp)
    {
        $this->otp = $otp; // Simpan OTP ke properti class
    }

    // Method untuk membangun email
    public function build(): self
    {
        return $this
            ->subject('Kode OTP Anda') // Subject email
            ->view('emails.otp') // View email OTP
            ->with([ // Data yang dikirim ke view
                'otp' => $this->otp, // Kode OTP
            ]);
    }
}