<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Role extends Model
{
    // HAPUS 'use HasFactory' jika tidak punya file database/factories/RoleFactory.php
    // use HasFactory; 

    protected $fillable = ['name'];

    /**
     * @return BelongsToMany<User,$this >
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'account_roles');
    }
}
