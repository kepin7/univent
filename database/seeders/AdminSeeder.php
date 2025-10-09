<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'hizkiakevin8@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin_univent'),
                'email_verified_at' => now()
            ]
        );
        DB::table('account_roles')->insert([
            'user_id' => $admin->id,
            'role_id' => 1, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}