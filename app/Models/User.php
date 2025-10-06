<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * Kolom yang bisa diisi (fillable).
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'otp_code',
        'otp_expires_at',
        'is_active',
        'reset_token',
        'reset_expires_at',
        'email_verified_at',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'reset_token',
    ];

    /**
     * Casting kolom ke tipe data tertentu.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at'    => 'datetime',
        'reset_expires_at'  => 'datetime',
        'is_active'         => 'boolean',
    ];

    /**
     * Ambil role user (dari Spatie).
     */
    public function getRoleNamesAttribute()
    {
        return $this->getRoleNames();
    }
}