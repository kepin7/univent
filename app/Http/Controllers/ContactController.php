<?php

namespace App\Http\Controllers; // Namespace controller

use App\Mail\ContactNotification; // Mailable notifikasi contact
use App\Models\Contact; // Model Contact
use Illuminate\Http\Request; // HTTP request
use Illuminate\Support\Facades\Auth; // Auth facade
use Illuminate\Support\Facades\Log; // Logging error
use Illuminate\Support\Facades\Mail; // Kirim email
use Illuminate\View\View; // Return View
use Illuminate\Http\RedirectResponse; // Return Redirect

class ContactController extends Controller
{
    public function __construct()
    {
        // Middleware auth → user wajib login
        $this->middleware('auth');

        // Middleware untuk mencegah admin mengakses fitur contact
        $this->middleware(function (Request $request, $next) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user(); // Ambil user login

            // Jika user adalah admin
            if ($user && $user->isAdmin()) {
                // Redirect admin ke dashboard
                return redirect()->route('dashboard')
                    ->with('error', 'Admin tidak dapat menggunakan fitur Contact.');
            }

            return $next($request); // Lanjutkan request
        });
    }

    // Menampilkan halaman contact
    public function create(): View
    {
        return view('contact'); // View contact
    }

    /**
     * Menyimpan pesan kontak dari user
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user(); // User login (pasti ada)

        // Validasi input contact
        $validated = $request->validate([
            'name' => 'required|string|max:100', // Nama pengirim
            'email' => 'required|email', // Email pengirim
            'message' => 'required|string|max:500', // Isi pesan
        ]);

        // Simpan pesan ke database
        $contact = Contact::create([
            'user_id' => $user->id, // Relasi ke user
            'name' => $validated['name'], // Nama
            'email' => $validated['email'], // Email
            'message' => $validated['message'], // Pesan
        ]);

        // =========================
        // KIRIM EMAIL NOTIFIKASI
        // =========================

        try {
            // Kirim email ke admin
            Mail::to('univenttelkom@gmail.com')
                ->send(new ContactNotification($contact));
        } catch (\Throwable $e) {
            // Catat error jika email gagal dikirim
            Log::error('Gagal mengirim email contact: ' . $e->getMessage());
        }

        // Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()
            ->with('success', 'Pesan kontak Anda telah berhasil dikirim!');
    }
}