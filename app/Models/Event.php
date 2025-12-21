<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $event_title
 * @property string $status
 * @property string|null $event_poster
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Event extends Model
{
    protected $fillable = [
        'event_title',
        'organizer_name',
        'organizer_type',
        'event_category',
        'event_description',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'event_location',
        'registration_link',
        'contact_person',
        'event_poster',
        'status',
        'user_id', // Pastikan user_id masuk fillable
    ];

    /**
     * @return BelongsTo<User,$this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<EventRegistration,$this>
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function getOrganizerRegistrationIdAttribute(): ?int
    {
        $registration = $this->registrations()->first();

        // Fix: Pakai nullsafe operator
        return $registration?->id;
    }
}