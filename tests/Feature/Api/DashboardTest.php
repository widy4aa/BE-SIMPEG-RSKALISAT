<?php

namespace Tests\Feature\Api;

use App\Services\Security\JwtService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_endpoint_returns_success_for_direktur_hrd_and_pegawai(): void
    {
        $direktur = User::factory()->create(['role' => 'direktur']);
        $hrd = User::factory()->create(['role' => 'hrd']);
        $pegawai = User::factory()->create(['role' => 'pegawai']);

        $resDirektur = $this->withTokenFor($direktur)->getJson('/api/dashboard');
        $resDirektur->assertOk()->assertJsonPath('success', true);

        $resHrd = $this->withTokenFor($hrd)->getJson('/api/dashboard');
        $resHrd->assertOk()->assertJsonPath('success', true);

        $resPegawai = $this->withTokenFor($pegawai)->getJson('/api/dashboard');
        $resPegawai->assertOk()->assertJsonPath('success', true);
    }

    private function withTokenFor(User $user): self
    {
        $token = app(JwtService::class)->generate([
            'sub' => (string) $user->id,
            'role' => $user->role,
        ])['token'];

        return $this->withHeader('Authorization', "Bearer {$token}");
    }
}
