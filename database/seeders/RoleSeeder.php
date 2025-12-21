<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // FIX: Gunakan upsert agar idempoten
        DB::table('roles')->upsert(
            [
                ['id' => 1, 'name' => 'admin', 'guard_name' => 'web'],
                ['id' => 2, 'name' => 'user', 'guard_name' => 'web'],
            ],
            'id', // Kunci untuk identifikasi (jika ID sudah ada, update)
            ['name', 'guard_name'] // Kolom yang di-update
        );
    }
}