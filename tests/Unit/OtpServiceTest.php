<?php

namespace Tests\Unit;

use App\Mail\SendOtpMail;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    // Gunakan RefreshDatabase untuk memastikan database bersih sebelum/sesudah tes
    use RefreshDatabase; 

    protected OtpService $otpService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Instance Service
        $this->otpService = new OtpService();
        // Mocking user: Kita tidak perlu data penuh, cukup yang relevan untuk OTP
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);
        Mail::fake(); // Memalsukan pengiriman email
    }

    // ===========================================
    // TEST JALUR 5: generateAndSend()
    // ===========================================
    public function test_generate_and_send_creates_otp_and_sends_mail()
    {
        $this->otpService->generateAndSend($this->user, 5); // 5 menit

        // Assert 1: Database memiliki OTP
        $user = $this->user->fresh();
        $this->assertNotNull($user->otp_code);
        $this->assertNotNull($user->otp_expires_at);
        
        // Assert 2: OTP expires dalam 5 menit (dengan sedikit toleransi)
        $this->assertTrue(Carbon::now()->addMinutes(4)->lt($user->otp_expires_at)); 

        // Assert 3: Email terkirim
        Mail::assertSent(SendOtpMail::class);
    }

    // ===========================================
    // TEST JALUR 1: verify() - Gagal: Belum Ada OTP
    // ===========================================
    public function test_verify_fails_if_no_otp_exists()
    {
        // Kondisi awal: otp_code dan otp_expires_at adalah null
        $this->assertFalse($this->otpService->verify($this->user, '123456'));
    }
    
    // ===========================================
    // TEST JALUR 2: verify() - Gagal: OTP Kadaluarsa
    // ===========================================
    public function test_verify_fails_if_otp_is_expired()
    {
        // Set OTP di database ke masa lalu
        $this->user->otp_code = '123456';
        $this->user->otp_expires_at = Carbon::now()->subMinute();
        $this->user->save();

        $this->assertFalse($this->otpService->verify($this->user->fresh(), '123456'));
    }

    // ===========================================
    // TEST JALUR 3: verify() - Gagal: OTP Salah
    // ===========================================
    public function test_verify_fails_if_otp_is_incorrect()
    {
        // Set OTP valid (tidak kadaluarsa)
        $this->user->otp_code = '123456';
        $this->user->otp_expires_at = Carbon::now()->addMinute();
        $this->user->save();

        // Coba verifikasi dengan OTP yang salah
        $this->assertFalse($this->otpService->verify($this->user->fresh(), '654321'));
    }

    // ===========================================
    // TEST JALUR 4: verify() - Sukses
    // ===========================================
    public function test_verify_succeeds_with_correct_and_unexpired_otp()
    {
        // Set OTP valid
        $this->user->otp_code = '123456';
        $this->user->otp_expires_at = Carbon::now()->addMinute();
        $this->user->save();

        // Coba verifikasi dengan OTP yang benar
        $this->assertTrue($this->otpService->verify($this->user->fresh(), '123456'));
    }
    
    // ===========================================
    // TEST JALUR 6: reset()
    // ===========================================
    public function test_reset_clears_otp_fields()
    {
        // Beri nilai OTP sebelum direset
        $this->user->otp_code = '999999';
        $this->user->otp_expires_at = Carbon::now()->addHour();
        $this->user->save();
        
        // Aksi reset
        $this->otpService->reset($this->user->fresh());

        // Assert: Cek kembali dari database, harus null
        $user = $this->user->fresh();
        $this->assertNull($user->otp_code);
        $this->assertNull($user->otp_expires_at);
    }
}