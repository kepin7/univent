<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminEventListControllerTest extends TestCase
{
    use DatabaseMigrations;
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $normalUser;
    protected Event $pendingEvent;
    
    // Ini harus 'user_id' sesuai Model dan Migrasi yang baru diperbaiki
    private string $eventUserIdColumn = 'user_id'; 

    // Inisialisasi data sebelum setiap test
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Setup Role & User
        Role::unguard();
        Role::firstOrCreate(['id' => 1], ['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['id' => 2], ['name' => 'user', 'guard_name' => 'web']);
        Role::reguard();
        
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $this->adminUser->roles()->attach(1);

        $this->normalUser = User::create([
            'name' => 'Normal User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'is_active' => true, 
        ]);
        $this->normalUser->roles()->attach(2);

        // 2. Setup Event & Registration
        Event::unguard(); 

        // Menggunakan semua kolom NOT NULL sesuai migrasi
        $this->pendingEvent = Event::create([
            // Kolom Foreign Key yang benar
            $this->eventUserIdColumn => $this->normalUser->id, 
            
            'event_title' => 'Test Event Pending',
            'event_description' => 'Ini adalah deskripsi test event.',
            'status' => 'pending',
            
            // Kolom wajib (NOT NULL) lainnya dari migrasi
            'organizer_name' => 'UniEvent Organizer',
            'organizer_type' => 'Komunitas',
            'event_category' => 'Technology',
            'start_date' => now()->addDays(7)->toDateString(),
            'start_time' => '10:00:00',
            'end_date' => now()->addDays(7)->toDateString(),
            'end_time' => '12:00:00',
            'event_location' => 'Virtual Meeting', 
            'registration_link' => 'https://register.test',
            'contact_person' => '081234567890',
            'event_poster' => null, 
        ]);
        
        Event::reguard(); 
        
        EventRegistration::create([
            'event_id' => $this->pendingEvent->id,
            'user_id' => $this->normalUser->id, 
            'status' => 'pending',
        ]);

        if (!Route::has('admin.events.approve')) {
             require base_path('routes/admin.php');
        }
    }

    // ----------------------------------------------------------------------
    // Path A: Approve Event Berhasil
    // ----------------------------------------------------------------------
    public function test_admin_can_approve_event_and_registrations(): void
    {
        $response = $this->actingAs($this->adminUser)->post(
            route('admin.events.approve', $this->pendingEvent->id)
        );

        $response->assertSessionHas('success')
                 ->assertRedirect(route('admin.event-list'));

        $this->assertDatabaseHas('events', [
            'id' => $this->pendingEvent->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $this->pendingEvent->id,
            'status' => 'approved',
        ]);
    }

    // ----------------------------------------------------------------------
    // Path C: Reject Event Berhasil
    // ----------------------------------------------------------------------
    public function test_admin_can_reject_event_and_registrations(): void
    {
        $response = $this->actingAs($this->adminUser)->post(
            route('admin.events.reject', $this->pendingEvent->id)
        );

        $response->assertSessionHas('success')
                 ->assertRedirect(route('admin.event-list'));

        $this->assertDatabaseHas('events', [
            'id' => $this->pendingEvent->id,
            'status' => 'rejected',
        ]);
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $this->pendingEvent->id,
            'status' => 'rejected',
        ]);
    }

    // ----------------------------------------------------------------------
    // Path D: Delete Event Berhasil
    // ----------------------------------------------------------------------
    public function test_admin_can_delete_event(): void
    {
        $response = $this->actingAs($this->adminUser)
                         ->withHeaders(['Referer' => route('admin.event-list')])
                         ->delete(route('admin.events.delete', $this->pendingEvent->id));

        $response->assertSessionHas('success')
                 ->assertRedirect(route('admin.event-list'));

        $this->assertDatabaseMissing('events', [
            'id' => $this->pendingEvent->id,
        ]);
        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $this->pendingEvent->id,
        ]);
    }
    
    // ----------------------------------------------------------------------
    // Path B & Error Handling: Event Tidak Ditemukan
    // ----------------------------------------------------------------------
    public function test_admin_actions_return_404_for_non_existent_event(): void
    {
        $nonExistentId = 999999;
        
        $this->actingAs($this->adminUser)
             ->post(route('admin.events.approve', $nonExistentId))
             ->assertStatus(404);

        $this->actingAs($this->adminUser)
             ->post(route('admin.events.reject', $nonExistentId))
             ->assertStatus(404);

        $this->actingAs($this->adminUser)
             ->delete(route('admin.events.delete', $nonExistentId))
             ->assertStatus(404);
    }

    // ----------------------------------------------------------------------
    // Test Keamanan: Non-Admin Tidak Boleh Mengakses
    // ----------------------------------------------------------------------
    public function test_non_admin_cannot_access_approval_routes(): void
    {
        $this->actingAs($this->normalUser)
             ->post(route('admin.events.approve', $this->pendingEvent->id))
             ->assertStatus(403);

        $this->actingAs($this->normalUser)
             ->delete(route('admin.events.delete', $this->pendingEvent->id))
             ->assertStatus(403);
    }
}