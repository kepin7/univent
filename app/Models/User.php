<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Mendefinisikan properti database agar terbaca PHPStan
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $avatar
 * @property string|null $google_id
 * @property string|null $otp_code
 * @property \Illuminate\Support\Carbon|null $otp_expires_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read \App\Models\Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasRoles, Notifiable;

    /** * Fix: Definisikan generic type untuk Factory 
     * @use HasFactory<\Database\Factories\UserFactory>
     */
    use HasFactory;

    /**
     * Field yang boleh diisi mass-assignment.
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
        'email_verified_at',
        'remember_token'
    ];

    /**
     * Field yang disembunyikan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting data.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'otp_expires_at' => 'datetime', // Tambahkan casting ini agar properti jadi Carbon
    ];

    // Fix: Tambahkan return type : bool
    public function isAdmin(): bool
    {
        return $this->roles()->where('name', 'admin')->exists();
    }

    // ============================================================
    // RELASI DAN ROLE SYSTEM (UNTUK ACCOUNT_ROLES)
    // ============================================================

    /**
     * Relasi ke table account_roles -> roles.
     * Ini override Spatie agar sesuai table kamu.
     */
    // Fix: Tambahkan return type BelongsToMany
    /**
     * @return BelongsToMany<Role,$this>
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Role::class,
            'account_roles',   // pivot table
            'user_id',         // FK ke users
            'role_id'          // FK ke roles
        );
    }

    /**
     * Mendapatkan nama role pertama user.
     */
    // Fix: Tambahkan return type ?string
    public function getRoleNameAttribute(): ?string
    {
        // Fix: Logic strict agar PHPStan tidak bingung "property on mixed"
        /** @var \App\Models\Role|null $role */
        $role = $this->roles()->first();

        return $role ? $role->name : null;
    }

    /**
     * Cek apakah user memiliki role tertentu.
     * Contoh: $user->hasRole("admin")
     */
    // Fix: Tambahkan tipe parameter dan return type
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    // ============================================================
    // RELASI LAIN
    // ============================================================

    /**
     * Relasi profil (one-to-one)
     */
    // Fix: Tambahkan return type HasOne
    /**
     * @return HasOne<Profile,$this>
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
}