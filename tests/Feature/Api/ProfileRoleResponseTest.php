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

class ProfileRoleResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_pegawai_roles_return_real_profile_shape_without_dummy_data(): void
    {
        foreach (['admin', 'hrd', 'direktur'] as $role) {
            $user = $this->createProfileUser($role);
            $token = app(JwtService::class)->generate([
                'sub' => (string) $user->id,
                'role' => $role,
            ])['token'];

            $response = $this->withHeader('Authorization', 'Bearer '.$token)
                ->getJson('/api/profile');

            $response->assertOk()
                ->assertJsonPath('success', true)
                ->assertJsonPath('data.role', $role)
                ->assertJsonPath('data.profile.label', 'Profile '.$role)
                ->assertJsonPath('data.profile.nama', ucfirst($role).' Real Profile')
                ->assertJsonPath('data.profile.no_kk', null)
                ->assertJsonMissing(['nama' => ucfirst($role).' Dummy'])
                ->assertJsonMissing(['catatan' => 'Data profile masih dummy.']);
        }
    }

    private function createProfileUser(string $role): User
    {
        $user = User::query()->create([
            'username' => 'profile_'.$role,
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
            'nama' => ucfirst($role).' Real Profile',
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
