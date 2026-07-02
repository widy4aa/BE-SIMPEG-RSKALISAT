<?php

namespace Tests\Feature\Api;

use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\User;
use App\Services\Notification\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Mockery;
use Tests\TestCase;

class ForgotPasswordOtpCooldownTest extends TestCase
{
    use RefreshDatabase;

    private const COOLDOWN_SECONDS = 60;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_request_otp_succeeds_and_returns_cooldown_seconds(): void
    {
        $pegawai = $this->seedPegawaiWithPhone();
        $this->fakeWhatsappExpecting(1);

        $response = $this->postJson('/api/forgot-password/request-otp', [
            'nik' => $pegawai->nik,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertSame(self::COOLDOWN_SECONDS, $response->json('cooldown_seconds'));
    }

    public function test_second_request_within_cooldown_is_blocked_without_sending(): void
    {
        $pegawai = $this->seedPegawaiWithPhone();
        // WhatsApp should be sent exactly once (first request only).
        $this->fakeWhatsappExpecting(1);

        $first = $this->postJson('/api/forgot-password/request-otp', [
            'nik' => $pegawai->nik,
        ]);
        $first->assertStatus(200);

        $second = $this->postJson('/api/forgot-password/request-otp', [
            'nik' => $pegawai->nik,
        ]);

        $second->assertStatus(429);
        $this->assertFalse($second->json('success'));
        $this->assertGreaterThan(0, $second->json('cooldown_seconds'));
        $this->assertLessThanOrEqual(self::COOLDOWN_SECONDS, $second->json('cooldown_seconds'));
        $this->assertStringContainsString('Harap tunggu', $second->json('message'));
    }

    public function test_request_otp_allowed_again_after_cooldown_expires(): void
    {
        $pegawai = $this->seedPegawaiWithPhone();
        // Two successful sends: one before, one after the cooldown clears.
        $this->fakeWhatsappExpecting(2);

        $this->postJson('/api/forgot-password/request-otp', ['nik' => $pegawai->nik])
            ->assertStatus(200);

        // Simulate cooldown expiry by clearing the rate limiter for this NIK.
        RateLimiter::clear('request-otp:' . $pegawai->nik);

        $this->postJson('/api/forgot-password/request-otp', ['nik' => $pegawai->nik])
            ->assertStatus(200);
    }

    private function fakeWhatsappExpecting(int $times): void
    {
        $mock = Mockery::mock(WhatsappService::class);
        $mock->shouldReceive('sendMessage')
            ->times($times)
            ->andReturn(['success' => true]);

        $this->app->instance(WhatsappService::class, $mock);
    }

    private function seedPegawaiWithPhone(): Pegawai
    {
        $user = User::query()->create([
            'username' => '3300000000000001',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'is_active' => true,
        ]);

        $pegawai = Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => '3300000000000001',
            'nama' => 'Siti Aminah',
            'status_pegawai' => 'aktif',
        ]);

        PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'no_telp' => '081234567890',
        ]);

        return $pegawai;
    }
}
