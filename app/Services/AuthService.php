<?php

namespace App\Services; // Mendefinisikan namespace untuk folder Services

use App\Models\User; // Mengimport model User
use Illuminate\Http\Request; // Mengimport class Request untuk menangani input
use Illuminate\Support\Facades\Auth; // Mengimport Facade Auth untuk autentikasi
use Illuminate\Support\Facades\Hash; // Mengimport Facade Hash untuk enkripsi password
use Illuminate\Support\Facades\Log; // Mengimport Facade Log untuk mencatat error
use Laravel\Socialite\Facades\Socialite; // Mengimport Socialite untuk login Google
use Illuminate\Http\RedirectResponse; // Mengimport tipe return response redirect
use Illuminate\Validation\Rules\Password; // Mengimport aturan validasi password Laravel

class AuthService
{
    protected OtpService $otpService; // Properti untuk menyimpan instance OtpService

    public function __construct(OtpService $otpService) // Constructor untuk dependency injection
    {
        $this->otpService = $otpService; // Menginisialisasi service OTP ke dalam properti
    }

    // Helper private untuk membersihkan input agar PHPStan tidak rewel
    private function getStringInput(Request $request, string $key): string
    {
        $value = $request->input($key); // Mengambil nilai input berdasarkan key
        return is_string($value) ? $value : ''; // Mengembalikan string kosong jika input bukan string
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([ // Melakukan validasi input registrasi
            'email' => 'required|email:rfc,dns|unique:users,email', // Email harus unik, format benar, dan DNS aktif
            'password' => [ // Aturan validasi password yang kompleks
                'required', // Harus diisi
                'confirmed', // Harus cocok dengan field password_confirmation
                Password::min(8) // Minimal 8 karakter
                    ->letters() // Harus mengandung huruf
                    ->mixedCase() // Harus ada huruf besar dan kecil
                    ->numbers() // Harus mengandung angka
                    ->symbols(), // Harus mengandung simbol
            ],
        ]);
        
        // Fix: Gunakan helper untuk memastikan string
        $email = $this->getStringInput($request, 'email'); // Ambil email sebagai string
        $password = $this->getStringInput($request, 'password'); // Ambil password sebagai string

        $parts = explode('@', $email); // Memecah email berdasarkan karakter '@'
        $name = $parts[0] ?: 'User'; // Mengambil bagian depan email sebagai nama, default 'User'

        /** @var User $user */
        $user = User::create([ // Menyimpan data user baru ke database
            'name' => $name, // Mengisi kolom nama
            'email' => $email, // Mengisi kolom email
            'password' => bcrypt($password), // Mengenkripsi password
            'is_active' => false, // Status awal tidak aktif sebelum verifikasi OTP
        ]);

        $user->assignRole('user'); // Memberikan role 'user' kepada pengguna baru
        $user->profile()->create(); // Membuat record profil kosong yang berelasi dengan user

        $this->otpService->generateAndSend($user); // Menghasilkan dan mengirimkan kode OTP ke email

        return redirect()->route('verification.notice', ['email' => $user->email]) // Redirect ke halaman verifikasi
            ->with('success', 'Akun berhasil dibuat. Silakan cek email untuk kode OTP.'); // Memberikan pesan sukses
    }

    public function loginWithEmail(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = User::where('email', $request->email)->first(); // Mencari user berdasarkan email di database

        // Fix: Hash check dengan input yang dipastikan string
        $inputPassword = $this->getStringInput($request, 'password'); // Ambil password dari input
        $userPassword = $user ? (string) $user->password : ''; // Ambil password terenkripsi dari DB

        if (! $user || ! Hash::check($inputPassword, $userPassword)) { // Validasi apakah user ada dan password cocok
            return back()->withInput()->with('error', 'Email atau password salah'); // Kembali jika gagal
        }

        $isTrusted = $request->cookie('device_verified_' . $user->id); // Mengecek cookie "trusted device"

        if ($isTrusted === 'true') { // Jika perangkat sudah pernah diverifikasi
            Auth::login($user, $request->has('remember')); // Langsung login tanpa OTP
            $request->session()->regenerate(); // Regenerasi session untuk keamanan
            return redirect()->route('dashboard')->with('success', 'Selamat datang kembali!'); // Redirect ke dashboard
        }

        // Jika tidak trusted, simpan status remember ke session dan kirim OTP
        $request->session()->put('auth.remember', $request->has('remember')); // Simpan preferensi "remember me"
        $this->otpService->generateAndSend($user); // Kirim OTP karena perangkat tidak dikenali

        return redirect()->route('verification.notice', ['email' => $user->email]) // Redirect ke halaman verifikasi
            ->with('success', 'Kode OTP telah dikirim ke email Anda'); // Memberikan pesan instruksi
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([ // Validasi input OTP
            'email' => 'required|email', // Email harus valid
            'otp' => 'required|digits:6', // OTP harus berjumlah 6 digit angka
        ]);

        /** @var User|null $user */
        $user = User::where('email', $request->email)->first(); // Cari user berdasarkan email

        if (! $user) { // Jika user tidak ditemukan
            return back()->with('error', 'User tidak ditemukan'); // Kembali dengan pesan error
        }

        $otp = $this->getStringInput($request, 'otp'); // Ambil nilai OTP sebagai string

        if (! $this->otpService->verify($user, $otp)) { // Cek validitas OTP melalui OtpService
            return back()->withInput()->with('error', 'OTP tidak valid atau sudah kadaluarsa'); // Kembali jika salah
        }

        $this->otpService->reset($user); // Hapus OTP dari sistem karena sudah berhasil digunakan

        $user->email_verified_at = now(); // Tandai email sudah terverifikasi
        $user->is_active = true; // Aktifkan status user
        $user->save(); // Simpan perubahan ke database

        $remember = (bool) $request->session()->pull('auth.remember', false); // Ambil status remember dari session
        Auth::login($user, $remember); // Proses login user ke sistem
        $request->session()->regenerate(); // Regenerasi ID session

        $cookie = cookie('device_verified_' . $user->id, 'true', 43200); // Buat cookie "trusted device" selama 30 hari
        return redirect()->route('dashboard')->with('success', 'Verifikasi berhasil! Selamat datang.')->withCookie($cookie); // Redirect dengan cookie
    }

    public function redirectToGoogle(): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
        $driver = Socialite::driver('google'); // Memanggil driver Socialite Google

        return $driver->stateless()->redirect(); // Mengarahkan user ke halaman login Google tanpa session state
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            /** @var \Laravel\Socialite\Two\AbstractProvider $driver */
            $driver = Socialite::driver('google'); // Memanggil driver Google

            /** @var \Laravel\Socialite\Contracts\User $googleUser */
            $googleUser = $driver->stateless()->user(); // Mengambil data user dari API Google

            /** @var User|null $user */
            $user = User::where('email', $googleUser->getEmail())->first(); // Cek apakah email sudah terdaftar

            if (! $user) { // Jika user belum terdaftar (Registrasi Baru via Google)
                $googleAvatar = null; // Inisialisasi variabel avatar
                $avatarUrl = $googleUser->getAvatar(); // Ambil URL foto profil dari Google

                if ($avatarUrl) { // Jika ada foto profil
                    $imageContent = file_get_contents($avatarUrl); // Ambil konten gambar dari URL
                    if ($imageContent !== false) { // Jika pengambilan gambar berhasil
                        $googleAvatar = base64_encode($imageContent); // Simpan gambar dalam format Base64
                    }
                }

                /** @var User $user */
                $user = User::create([ // Buat user baru di database
                    'name' => $googleUser->getName(), // Ambil nama dari Google
                    'email' => $googleUser->getEmail(), // Ambil email dari Google
                    'google_id' => $googleUser->getId(), // Simpan ID unik Google
                    'avatar' => $googleAvatar, // Simpan avatar Base64
                    'password' => Hash::make(str()->random(16)), // Buat password random yang kuat
                    'email_verified_at' => now(), // Otomatis terverifikasi karena dari Google
                    'is_active' => true, // Langsung aktif
                ]);

                $user->assignRole('user'); // Berikan role user
                $user->profile()->create(); // Buat record profil
            } else { // Jika user sudah terdaftar (Update Data Google)
                $updateData = [ // Siapkan data yang akan diupdate
                    'google_id' => $googleUser->getId(), // Pastikan ID Google tersimpan
                    'name' => $googleUser->getName(), // Update nama sesuai profil Google terbaru
                    'email_verified_at' => $user->email_verified_at ?? now(), // Isi jika sebelumnya kosong
                    'is_active' => true, // Pastikan user aktif
                ];

                $currentAvatar = $user->avatar; // Ambil avatar yang ada sekarang
                $isLocalAvatar = $currentAvatar && ! str_starts_with($currentAvatar, 'http'); // Cek apakah avatar lokal

                if (! $isLocalAvatar) { // Jika bukan avatar lokal (misal masih URL lama atau kosong)
                    $avatarUrl = $googleUser->getAvatar(); // Ambil avatar terbaru dari Google
                    if ($avatarUrl) {
                        $imageContent = file_get_contents($avatarUrl); // Ambil konten gambar
                        if ($imageContent !== false) {
                            $updateData['avatar'] = base64_encode($imageContent); // Update avatar Base64
                        }
                    }
                }

                $user->update($updateData); // Simpan update data ke database
            }

            Auth::login($user); // Login user secara otomatis
            $request->session()->regenerate(); // Regenerasi session

            return redirect()->route('dashboard')->with('success', 'Login Google berhasil!'); // Redirect ke dashboard
        } catch (\Exception $e) { // Jika terjadi error saat proses OAuth
            Log::error('Google Login Error: ' . $e->getMessage()); // Catat pesan error ke log Laravel
            return redirect()->route('login')->with('error', 'Login Google gagal: ' . $e->getMessage()); // Redirect balik ke login
        }
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate([ // Validasi input email untuk lupa password
            'email' => 'required|email:rfc,dns', // Format email harus valid
        ]);

        /** @var User|null $user */
        $user = User::where('email', $request->email)->first(); // Cari user berdasarkan email

        if (! $user) { // Jika email tidak ada di database
            return back()->withInput()->with('error', 'Email tidak ditemukan dalam sistem kami'); // Kembali dengan error
        }

        $this->otpService->generateAndSend($user); // Kirim kode OTP untuk proses reset password

        return redirect()->route('password.reset.form', ['email' => $user->email]) // Redirect ke form input password baru
            ->with('success', 'Kode reset password dikirim ke email Anda'); // Pesan sukses
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([ // Validasi input reset password
            'email' => 'required|email:rfc,dns', // Validasi email
            'otp' => 'required|digits:6', // Validasi OTP 6 digit
            'password' => [ // Validasi password baru sesuai standar keamanan
                    'required',
                    'confirmed',
                    Password::min(8)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
        ]);
        
        /** @var User|null $user */
        $user = User::where('email', $request->email)->first(); // Cari user

        if (! $user) { // Jika user tidak ditemukan
            return back()->withInput()->with('error', 'User tidak ditemukan'); // Kembali dengan error
        }

        $otp = $this->getStringInput($request, 'otp'); // Ambil OTP dari input

        if (! $this->otpService->verify($user, $otp)) { // Verifikasi apakah OTP cocok
            return back()->withInput()->with('error', 'OTP tidak valid atau kadaluarsa'); // Kembali jika salah
        }

        $password = $this->getStringInput($request, 'password'); // Ambil password baru
        $user->password = Hash::make($password); // Enkripsi password baru
        $this->otpService->reset($user); // Hapus OTP yang sudah terpakai
        $user->save(); // Simpan password baru ke DB

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login dengan password baru.'); // Redirect ke login
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = User::where('email', $request->email)->first(); // Cari user berdasarkan email

        if (! $user) { // Jika user tidak ditemukan
            return back()->with('error', 'User tidak ditemukan'); // Kembali dengan error
        }

        try {
            $this->otpService->resendOtp($user); // Panggil fungsi resend pada service
            return back()->with('success', 'Kode OTP baru telah dikirim'); // Pesan sukses
        } catch (\Exception $e) { // Jika gagal (misal kena limit kirim)
            return back()->with('error', $e->getMessage()); // Tampilkan pesan error dari exception
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout(); // Melakukan proses logout user dari guard web

        $request->session()->invalidate(); // Menghapus seluruh data session
        $request->session()->regenerateToken(); // Regenerasi CSRF token untuk mencegah serangan CSRF

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.'); // Redirect ke login
    }

    public function logoutGoogle(Request $request): RedirectResponse
    {
        return $this->logout($request); // Memanggil fungsi logout yang sama untuk user Google
    }
}