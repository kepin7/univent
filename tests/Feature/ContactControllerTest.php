<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Memalsukan (fake) Mail untuk mencegah email sungguhan terkirim saat pengujian
        Mail::fake(); 
        
        // Setup User & Roles
        $this->seed(['RoleSeeder']); 
        
        // Buat Admin (role_id=1)
        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach(1); 
        
        // Buat User biasa (role_id=2)
        $this->regularUser = User::factory()->create();
    }

    // ===========================================
    // TEST JALUR 1: Sukses Akses (Regular User)
    // ===========================================
    
    /**
     * Test 1: User biasa berhasil mengakses halaman kontak.
     */
    public function test_regular_user_can_access_contact_form()
    {
        $response = $this->actingAs($this->regularUser)
                         ->get(route('contact'));

        $response->assertStatus(200);
    }

    // ===========================================
    // TEST JALUR 2: Gagal Akses (Admin)
    // ===========================================
    
    /**
     * Test 2: Admin GAGAL mengakses halaman kontak dan dialihkan ke dashboard.
     */
    public function test_admin_cannot_access_contact_form()
    {
        $response = $this->actingAs($this->adminUser)
                         ->get(route('contact'));

        // Assert: Redirect ke dashboard (rute default Admin)
        $response->assertRedirect(route('dashboard')); 
        // Assert: Harus memiliki pesan error
        $response->assertSessionHas('error', 'Admin tidak dapat menggunakan fitur Contact.');
    }

    // ===========================================
    // TEST JALUR 3: Validasi Gagal
    // ===========================================
    
    /**
     * Test 3: Pengiriman form GAGAL jika data tidak valid (misal, email salah format).
     */
    public function test_contact_submission_fails_on_invalid_data()
    {
        // Data yang tidak valid (email salah)
        $invalidData = [
            'name' => 'John Doe',
            'email' => 'email-invalid', // Harusnya gagal
            'message' => 'Ini pesan uji coba.',
        ];

        $response = $this->actingAs($this->regularUser)
                         ->post(route('contact.store'), $invalidData);

        // Assert 1: Redirect kembali dengan error validasi
        $response->assertSessionHasErrors(['email']);
        
        // Assert 2: Database TIDAK memiliki catatan baru
        $this->assertDatabaseCount('contacts', 0);
    }

    // ===========================================
    // TEST JALUR 4: Sukses Penuh (Happy Path)
    // ===========================================
    
    /**
     * Test 4: Pengiriman form kontak BERHASIL.
     */
    public function test_contact_submission_is_successful()
    {
        $validData = [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@test.com',
            'message' => 'Pesan sukses dari user.',
        ];

        $response = $this->actingAs($this->regularUser)
                         ->post(route('contact.store'), $validData);

        // Assert 1: Redirect kembali dengan pesan sukses
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pesan kontak Anda telah berhasil dikirim!');

        // Assert 2: Database memiliki catatan baru
        $this->assertDatabaseHas('contacts', [
            'user_id' => $this->regularUser->id,
            'name' => 'Jane Smith',
            'email' => 'jane.smith@test.com',
        ]);
        
        // Assert 3: Email notifikasi dikirim
        // Ini memastikan Path 5.1 (Mail::to()->send) tereksekusi
        Mail::assertSent(\App\Mail\ContactNotification::class);
    }
}