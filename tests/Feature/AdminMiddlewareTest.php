<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role; // PASTIKAN PATH INI BENAR SESUAI LOKASI MODEL ROLE ANDA
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    // Jalankan migrasi hanya sekali per class (untuk membuat tabel)
    use DatabaseMigrations; 
    // Gunakan transaksi untuk membersihkan data setelah setiap test method
    use DatabaseTransactions; 

    private string $testRoute = '/test-admin-access';

    protected function setUp(): void
    {
        parent::setUp();

        // FIX PENTING: Matikan Mass Assignment Protection sementara
        \App\Models\Role::unguard(); 
        
        // 1. Setup Data: Buat Role setelah migrasi berjalan
        // Masukkan semua field yang NOT NULL, termasuk guard_name
        Role::firstOrCreate(
            ['id' => 1],
            ['name' => 'admin', 'guard_name' => 'web'] 
        );
        Role::firstOrCreate(
            ['id' => 2],
            ['name' => 'user', 'guard_name' => 'web']
        );

        // Aktifkan kembali Mass Assignment Protection
        \App\Models\Role::reguard(); 
        
        // 2. Definisikan Route dummy untuk diuji dengan AdminMiddleware
        Route::middleware(['web', 'admin'])
            ->any($this->testRoute, function () {
                return response('Admin Allowed', 200);
            });
    }

    // Path A: User TIDAK LOGIN (Guest)
    public function test_middleware_blocks_guest_user_access(): void
    {
        $this->get($this->testRoute)->assertStatus(403);
    }

    // Path B: User LOGIN tapi BUKAN ADMIN
    public function test_middleware_blocks_non_admin_user_access(): void
    {
        // Setup Data Mock: Buat user biasa (assign role id 2 = user)
        /** @var User $user */
        $user = User::factory()->create();
        $user->roles()->attach(2); // Assign role 'user'
        
        $this->actingAs($user)->get($this->testRoute)->assertStatus(403);
    }

    // Path C: User LOGIN dan ADMIN
    public function test_middleware_allows_admin_user_access(): void
    {
        // Setup Data Mock: Buat user admin (assign role id 1 = admin)
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->roles()->attach(1); // Assign role 'admin'
        
        $this->actingAs($admin)->get($this->testRoute)->assertStatus(200);
    }
}