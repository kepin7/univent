<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Carbon\Carbon;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected string $originalPassword = 'oldpassword123';
    protected string $originalName = 'Original Test User'; 

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(['RoleSeeder']); 
        
        // Setup User dengan data jelas
        $this->user = User::factory()->create([
            'name' => $this->originalName, 
            'password' => Hash::make($this->originalPassword),
            'email' => 'testuser@example.com',
        ]);
        $this->user->profile()->create([
            'birthday' => '1990-01-01',
            'phone' => '081234567890',
        ]);
    }

    // ===========================================
    // TEST JALUR 1: show()
    // ===========================================
    public function test_authenticated_user_can_view_profile()
    {
        $response = $this->actingAs($this->user)->get(route('profile.show'));
        $response->assertStatus(200);
        $response->assertViewIs('profile'); 
    }
    
    // ===========================================
    // TEST JALUR 2: update() - Validasi Gagal (Sesuai Aturan Anda)
    // ===========================================
    /**
     * Test validasi gagal pada field yang dihandle (format tanggal).
     */
    public function test_profile_update_fails_on_invalid_birthday()
    {
        // Menguji aturan 'nullable|date'
        $response = $this->actingAs($this->user)->put(route('profile.update'), [ 
            'phone' => '081122334455',
            'birthday' => 'BUKAN-TANGGAL', // Input invalid
        ]);

        // Assert: Pastikan validasi gagal
        $response->assertInvalid('birthday'); 
        
        // Pastikan data lama tidak berubah
        $this->assertEquals('081234567890', $this->user->profile->fresh()->phone);
    }

    // ===========================================
    // TEST JALUR 3: update() - Sukses Update Detail
    // ===========================================
    /**
     * Test sukses update phone dan birthday.
     */
    public function test_profile_update_succeeds_details_only()
    {
        $newPhone = '087766554433';
        $newBirthday = '1999-12-31';

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'phone' => $newPhone,
            'birthday' => $newBirthday,
        ]);

        $response->assertRedirect(route('profile.edit')); 
        $response->assertSessionHas('success');

        $freshUser = $this->user->fresh();
        
        // Assert: Profile Detail terupdate
        $this->assertEquals($newPhone, $freshUser->profile->phone); 
        // ✅ PERBAIKAN: Gunakan toDateString() untuk membandingkan tanggal
        $this->assertEquals($newBirthday,
    Carbon::parse($freshUser->profile->birthday)->toDateString()
); 
        
        // Assert: Pastikan Name dan Password TIDAK berubah 
        $this->assertEquals($this->originalName, $freshUser->name);
        $this->assertTrue(Hash::check($this->originalPassword, $freshUser->password)); 
    }
    // ===========================================
    // TEST JALUR 4: update() - Sukses dengan Avatar
    // ===========================================
    /**
     * Test sukses update avatar.
     */
    public function test_profile_update_succeeds_with_avatar()
    {
        $base64Image = base64_encode('fake-image-data');
        
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'phone' => '081234567890', 
            'birthday' => '1990-01-01',
            'new_avatar_temp' => $base64Image, // Field yang diuji
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $freshUser = $this->user->fresh();
        
        // Assert: Avatar terupdate
        $this->assertStringContainsString($base64Image, $freshUser->avatar); 
    }

    // ===========================================
    // TEST JALUR 5: update() - Sukses menghapus Avatar
    // ===========================================
    /**
     * Test sukses menghapus avatar.
     */
    public function test_profile_update_succeeds_with_remove_avatar()
    {
        // Beri user avatar dummy terlebih dahulu
        $this->user->update(['avatar' => 'some-base64-data']);

        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'remove_avatar' => true, // Field yang diuji
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $freshUser = $this->user->fresh();
        
        // Assert: Avatar diubah menjadi NULL
        $this->assertNull($freshUser->avatar); 
    }
}