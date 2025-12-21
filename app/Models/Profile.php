<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // <--- WAJIB IMPORT INI

class Profile extends Model
{

    protected $fillable = [
        'user_id',
        'birthday',
        'phone',
    ];

    protected $casts = [
        'birthday' => 'date', // FIX: Tambahkan cast date
    ];

    /**
     * Mendapatkan user yang memiliki profil ini.
     */
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}