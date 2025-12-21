<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'message',
    ];

    /**
     * @return BelongsTo<User, Contact>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, Contact> */
        return $this->belongsTo(User::class);
    }
}