<?php

namespace App\Http\Middleware; // Namespace middleware

use Carbon\Carbon; // Library untuk manipulasi waktu/tanggal
use Closure; // Closure untuk middleware next
use Illuminate\Http\Request; // HTTP Request
use Laravel\Sanctum\PersonalAccessToken; // Model token Sanctum
use Symfony\Component\HttpFoundation\Response; // Tipe Response HTTP

class CheckTokenExpiry // Middleware untuk cek masa berlaku token
{
    // Method handle dijalankan setiap request yang melewati middleware ini
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil token dari Authorization: Bearer <token>
        $token = $request->bearerToken();

        // Jika token tidak ada di request
        if (! $token) {
            return response()->json(
                ['message' => 'Token tidak ditemukan'], // Pesan error
                401 // HTTP Unauthorized
            );
        }

        // Cari token di tabel personal_access_tokens
        $accessToken = PersonalAccessToken::findToken($token);

        // Jika token tidak ditemukan / tidak valid
        if (! $accessToken) {
            return response()->json(
                ['message' => 'Token tidak valid'], // Pesan error
                401 // HTTP Unauthorized
            );
        }

        // Jika token memiliki expiry dan sudah melewati waktu sekarang
        if (
            $accessToken->expires_at && // Pastikan expires_at tidak null
            Carbon::now()->greaterThan($accessToken->expires_at) // Bandingkan waktu
        ) {
            return response()->json(
                ['message' => 'Token sudah kedaluwarsa'], // Pesan expired
                401 // HTTP Unauthorized
            );
        }

        // Jika token valid dan belum kedaluwarsa, lanjutkan request
        return $next($request);
    }
}