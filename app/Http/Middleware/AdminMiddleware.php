<?php

namespace App\Http\Middleware; // Namespace middleware

use Closure; // Closure untuk middleware next
use Illuminate\Http\Request; // HTTP Request
use Illuminate\Support\Facades\Auth; // Facade Auth
use Symfony\Component\HttpFoundation\Response; // Tipe Response wajib

class AdminMiddleware // Middleware admin
{
    // Method handle dijalankan setiap request yang melewati middleware ini
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (Auth::check()) {

            /** @var \App\Models\User|null $user */
            $user = Auth::user(); // Ambil user yang sedang login
            
            // Pastikan user ada dan memiliki role admin
            if ($user && $user->isAdmin()) {
                return $next($request); // Izinkan request dilanjutkan
            }
        }

        // Jika bukan admin atau belum login → akses ditolak
        abort(403, 'Akses ditolak'); // HTTP 403 Forbidden
    }
}
