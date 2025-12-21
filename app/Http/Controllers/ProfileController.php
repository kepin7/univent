<?php

namespace App\Http\Controllers; // Namespace controller

use Illuminate\Http\Request; // Class Request
use Illuminate\Support\Facades\Auth; // Facade Auth
use Illuminate\View\View; // Return type View
use Illuminate\Http\RedirectResponse; // Return type RedirectResponse

class ProfileController extends Controller // Controller Profile
{
    /**
     * Menampilkan halaman profil pengguna
     */
    public function show(): View|RedirectResponse // Method show profil
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user(); // Ambil user yang login

        // Jika user belum login
        if (! $user) {
            return redirect()->route('login'); // Redirect ke login
        }

        $user->load('profile'); // Load relasi profile

        return view('profile', compact('user')); // Tampilkan view profile
    }

    /**
     * Menampilkan halaman edit profil
     */
    public function edit(): View|RedirectResponse // Method edit profil
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user(); // Ambil user login

        // Jika user belum login
        if (! $user) {
            return redirect()->route('login'); // Redirect login
        }

        $user->load('profile'); // Load relasi profile

        return view('edit-profile', compact('user')); // Tampilkan form edit
    }

    /**
     * Update profil (avatar, birthday, phone)
     */
    public function updateProfile(Request $request): RedirectResponse // Update profil
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user(); // Ambil user login

        // Guard clause jika user null
        if (! $user) {
            return redirect()->route('login'); // Redirect login
        }

        // Validasi input form
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Nama user
            'phone' => 'nullable|string|max:15|regex:/^(\+?\d{1,15})$/', // Nomor HP
            'birthday' => 'nullable|date', // Tanggal lahir
            'new_avatar_temp' => 'nullable|string', // Avatar base64
            'remove_avatar' => 'nullable|boolean', // Flag hapus avatar
        ]);

        $user->name = $validated['name']; // Update nama user

        /*
        |--------------------------------------------------------------------------
        | HANDLE AVATAR
        |--------------------------------------------------------------------------
        */

        // Jika user memilih hapus avatar
        if ($request->remove_avatar == 1) {
            $user->avatar = null; // Set avatar null
        }

        // Jika user upload avatar baru
        if ($request->filled('new_avatar_temp')) {

            $temp = $request->input('new_avatar_temp'); // Ambil data avatar
            $base64 = is_string($temp) ? $temp : ''; // Pastikan string

            // Jika format data:image/png;base64,xxxx
            if (str_contains($base64, ',')) {
                $parts = explode(',', $base64); // Pisahkan header & data
                $base64 = $parts[1] ?? $base64; // Ambil data base64 saja
            }

            $user->avatar = $base64; // Simpan avatar base64
        }

        $user->save(); // Simpan perubahan user

        /*
        |--------------------------------------------------------------------------
        | HANDLE PROFILE DETAIL (phone & birthday)
        |--------------------------------------------------------------------------
        */

        // Update atau buat data profile user
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id], // Kondisi pencarian
            [
                'birthday' => $validated['birthday'] ?? null, // Birthday
                'phone' => $validated['phone'] ?? null, // Phone
            ]
        );

        // Redirect kembali ke halaman edit dengan pesan sukses
        return redirect()->route('profile.edit')
            ->with('success', 'Profile berhasil diperbarui!');
    }
}