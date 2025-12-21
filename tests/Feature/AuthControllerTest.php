<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase; 

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected OtpService $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        // Memalsukan (fake) email
        Mail::fake(); 
        
        // Instansiasi OtpService (sudah teruji Unit)
        $this->otpService = new OtpService(); 
        
        // Seeder diperlukan untuk role
        $this->seed(['RoleSeeder']); 
    }

    // ===========================================
    // TEST JALUR 1: register() - Sukses
    // ===========================================
    public function test_register_creates_user_profile_and_redirects_to_verification()
    {
        $validData = [
            'email' => 'newuser@test.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->post(route('register'), $validData); 

        $response->assertRedirect(route('verification.notice', ['email' => 'newuser@test.com'])); 

        $user = User::where('email', 'newuser@test.com')->first();
        $this->assertNotNull($user);
        $this->assertNotNull($user->profile);
    }
    
    // ===========================================
    // TEST JALUR 2 & 3: verifyOtp() - Gagal
    // ===========================================
    
    public function test_verify_otp_fails_if_user_not_found()
    {
        $response = $this->post(route('verification.verify'), [
            'email' => 'unknown@user.com', 
            'otp' => '123456',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'User tidak ditemukan');
    }

    public function test_verify_otp_fails_if_otp_is_invalid()
    {
        $user = User::factory()->create(['is_active' => false]);
        $this->otpService->generateAndSend($user);
        
        $response = $this->post(route('verification.verify'), [
            'email' => $user->email,
            'otp' => '654321', // OTP salah
        ]);

        $response->assertRedirect();
        // PERBAIKAN: Menggunakan string 'OTP tidak valid atau sudah kadaluarsa'
        $response->assertSessionHas('error', 'OTP tidak valid atau sudah kadaluarsa'); 
        $this->assertFalse($user->fresh()->is_active);
    }

    // ===========================================
    // TEST JALUR 4: verifyOtp() - Sukses
    // ===========================================
    public function test_verify_otp_succeeds()
    {
        $user = User::factory()->create(['is_active' => false, 'email_verified_at' => null]);
        $this->otpService->generateAndSend($user, 10);
        $otpCode = $user->fresh()->otp_code; 
        
        $response = $this->post(route('verification.verify'), [
            'email' => $user->email,
            'otp' => $otpCode, 
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');
        
        $freshUser = $user->fresh();
        $this->assertTrue($freshUser->is_active);
        $this->assertNull($freshUser->otp_code);
        $this->assertTrue(Auth::check()); 
    }

    // ===========================================
    // TEST JALUR 5 & 6: forgotPassword()
    // ===========================================

    public function test_forgot_password_fails_if_user_not_found()
    {
        $response = $this->post(route('password.email'), ['email' => 'unknown@user.com']);
        $response->assertRedirect();
        // PERBAIKAN: Menggunakan string 'Email tidak ditemukan'
        $response->assertSessionHas('error', 'Email tidak ditemukan'); 
        Mail::assertNothingSent();
    }
    
    public function test_forgot_password_succeeds()
    {
        $user = User::factory()->create();
        
        $response = $this->post(route('password.email'), ['email' => $user->email]);

        $response->assertRedirect(route('password.reset.form', ['email' => $user->email]));
        $response->assertSessionHas('success');
        
        $this->assertNotNull($user->fresh()->otp_code);
    }
    
    // ===========================================
    // TEST JALUR 7 & 8: resetPassword()
    // ===========================================

    public function test_reset_password_fails_on_invalid_otp()
    {
        $user = User::factory()->create();
        $this->otpService->generateAndSend($user);
        
        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'otp' => '999999',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'OTP tidak valid atau kadaluarsa');
        
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_reset_password_succeeds()
    {
        $user = User::factory()->create();
        $this->otpService->generateAndSend($user);
        $otpCode = $user->fresh()->otp_code; 
        
        $response = $this->post(route('password.update'), [
            'email' => $user->email,
            'otp' => $otpCode,
            'password' => 'new_secure_password',
            'password_confirmation' => 'new_secure_password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Password berhasil direset. Silakan login dengan password baru.');
        
        $freshUser = $user->fresh();
        $this->assertTrue(Hash::check('new_secure_password', $freshUser->password));
        $this->assertNull($freshUser->otp_code);
    }

    // ===========================================
    // TEST JALUR 9: resendOtp() - Sukses
    // ===========================================

    public function test_resend_otp_succeeds()
    {
        $user = User::factory()->create();
        $this->otpService->generateAndSend($user); 
        $oldOtp = $user->fresh()->otp_code;

        $response = $this->post(route('verification.resend'), ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Kode OTP baru telah dikirim');
        $this->assertNotEquals($oldOtp, $user->fresh()->otp_code); 
        Mail::assertSent(\App\Mail\SendOtpMail::class);
    }
}