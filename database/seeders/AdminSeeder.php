<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admin = User::firstOrCreate(
            ['email' => 'hizkiakevin8@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin_univent'),
                'role' => 'admin',
                'email_verified_at' => now()
            ]
        );
        $admin->assignRole('admin');
        }
    }