<?php

namespace App\Mail; // Namespace untuk Mailable

use App\Models\Contact; // Model Contact
use Illuminate\Bus\Queueable; // Trait untuk queue email
use Illuminate\Mail\Mailable; // Base class Mailable
use Illuminate\Queue\SerializesModels; // Trait serialisasi model

class ContactNotification extends Mailable // Class email notifikasi
{
    use Queueable, SerializesModels; // Aktifkan queue & serialisasi

    public Contact $contact; // Properti untuk menyimpan data contact

    // Constructor menerima data contact
    public function __construct(Contact $contact)
    {
        $this->contact = $contact; // Simpan data contact ke properti
    }

    // Membangun isi email
    public function build(): self
    {
        return $this
            ->subject('New contact Received') // Subject email
            ->view('emails.contact') // View email yang digunakan
            ->with([ // Data yang dikirim ke view
                'name' => $this->contact->name, // Nama pengirim
                'email' => $this->contact->email, // Email pengirim
                'messageContent' => $this->contact->message, // Isi pesan
            ]);
    }
}