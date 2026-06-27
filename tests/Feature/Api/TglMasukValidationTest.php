<?php

namespace Tests\Feature\Api;

use App\Models\Jabatan;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\UnitKerja;
use App\Models\User;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TglMasukValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_profile_rejects_future_tgl_masuk(): void
    {
        $user = $this->createProfileUser('pegawai');
        $token = app(JwtService::class)->generate([
            'sub' => (string) $user->id,
            'role' => 'pegawai',
        ])['token'];

        $futureDate = now()->addDays(5)->toDateString();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/profile', [
                'tgl_masuk' => $futureDate,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonFragment([
                'tgl_masuk tidak boleh melebihi tanggal sekarang',
            ]);
    }

    public function test_update_inti_rejects_future_tgl_masuk(): void
    {
        $hrd = $this->createProfileUser('hrd');
        $pegawai = Pegawai::first();

        $token = app(JwtService::class)->generate([
            'sub' => (string) $hrd->id,
            'role' => 'hrd',
        ])['token'];

        $futureDate = now()->addDays(5)->toDateString();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/hrd/pegawai/'.$pegawai->id.'/inti', [
                'tgl_masuk' => $futureDate,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonFragment([
                'tgl_masuk tidak boleh melebihi tanggal sekarang',
            ]);
    }

    private function createProfileUser(string $role): User
    {
        $user = User::query()->create([
            'username' => 'test_'.$role.'_'.uniqid(),
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);

        $unitKerja = UnitKerja::query()->create([
            'nama' => 'Unit '.$role,
        ]);

        $jabatan = Jabatan::query()->create([
            'nama' => 'Jabatan '.$role,
            'unit_kerja_id' => $unitKerja->id,
            'tmt_mulai' => now()->toDateString(),
        ]);

        $pegawai = Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => '99'.str_pad((string) $user->id, 14, '0', STR_PAD_LEFT),
            'nip' => null,
            'nama' => ucfirst($role).' Test',
            'jabatan_id' => $jabatan->id,
            'status_pegawai' => 'aktif',
        ]);

        PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'email' => $role.'@example.test',
            'no_telp' => null,
        ]);

        return $user;
    }
}
