<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\AccountRole;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'univenttelkom@gmail.com'],
            [
                'name' => 'Admin Univent',
                'password' => Hash::make('admin_univent'),
                'email_verified_at' => now(),
            ]
        );

        // FIX: Gunakan Model AccountRole::firstOrCreate
        AccountRole::firstOrCreate(
            ['user_id' => $admin->id, 'role_id' => 1],
            // Data tambahan jika baru dibuat
            ['created_at' => now(), 'updated_at' => now()]
        );
    }
}