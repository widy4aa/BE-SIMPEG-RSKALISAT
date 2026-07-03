<?php

namespace Tests\Feature\Api;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\Setting;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HrdPromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_hrd_can_get_default_promotion_settings(): void
    {
        $hrd = $this->createHrd();

        $response = $this->withTokenFor($hrd)->getJson('/api/hrd/promosi/settings');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.promosi_min_masa_kerja', 2)
            ->assertJsonPath('data.promosi_min_jp_diklat', 40)
            ->assertJsonPath('data.promosi_wajib_str_aktif', true);
    }

    public function test_hrd_can_update_promotion_settings(): void
    {
        $hrd = $this->createHrd();

        $response = $this->withTokenFor($hrd)->putJson('/api/hrd/promosi/settings', [
            'promosi_min_masa_kerja' => 3,
            'promosi_min_jp_diklat' => 80,
            'promosi_wajib_str_aktif' => false,
            'promosi_bobot_masa_kerja' => 50,
            'promosi_bobot_diklat' => 30,
            'promosi_bobot_pendidikan' => 20,
            'promosi_passing_grade' => 70,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.promosi_min_masa_kerja', 3)
            ->assertJsonPath('data.promosi_min_jp_diklat', 80)
            ->assertJsonPath('data.promosi_wajib_str_aktif', false);

        $this->assertDatabaseHas('settings', [
            'key' => 'promosi_min_masa_kerja',
            'value' => '3',
        ]);
    }

    public function test_hrd_can_get_promotion_recommendations(): void
    {
        $hrd = $this->createHrd();

        Pegawai::query()->create([
            'user_id' => $hrd->id,
            'nik' => '3174010101010099',
            'nama' => 'Pegawai Lama',
            'status_pegawai' => 'aktif',
            'tgl_masuk' => now()->subYears(5)->toDateString(),
        ]);

        $response = $this->withTokenFor($hrd)->getJson('/api/hrd/promosi/rekomendasi');

        $response->assertOk()
            ->assertJsonPath('success', true);
    }

    private function createHrd(): User
    {
        return User::query()->create([
            'username' => 'hrd_promosi',
            'password' => Hash::make('password'),
            'role' => 'hrd',
            'is_active' => true,
        ]);
    }

    private function withTokenFor(User $user): self
    {
        $token = app(JwtService::class)->generate([
            'sub' => (string) $user->id,
            'role' => $user->role,
        ])['token'];

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }
}
