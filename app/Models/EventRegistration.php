<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{


    // Nama tabel di database
    protected $table = 'event_registrations';

    // Kolom yang bisa diisi (sesuai migrasi Anda)
    protected $fillable = [
        'user_id',
        'event_id',
        'status',
    ];

    /**
     * Relasi ke Event: Setiap pendaftaran milik satu Event.
     */
    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relasi ke User: Setiap pendaftaran milik satu User.
     */
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}