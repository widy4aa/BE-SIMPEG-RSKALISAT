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

class HrdDiklatMutationTest extends TestCase
{
    use RefreshDatabase;

    public function test_hrd_can_create_internal_master_diklat(): void
    {
        $hrd = $this->createUserWithPegawai('hrd', '9600000000000001', 'HRD Create Internal');

        $response = $this->withTokenFor($hrd)->postJson('/api/hrd/diklat', [
            'nama_kegiatan' => 'Pelatihan Internal HRD',
            'kategori' => 'Manajemen',
            'jenis_diklat' => 'Manajerial',
            'penyelenggara' => 'RS Kalisat',
            'lokasi' => 'Ruang Rapat Utama',
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(6)->toDateString(),
            'jp' => 12,
            'jenis_pelaksana' => 'internal',
            'jenis_biaya' => 'Rumah Sakit',
            'total_biaya' => 1500000,
            'waktu' => '08:00',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Master Diklat berhasil dibuat.')
            ->assertJsonPath('data.nama_kegiatan', 'Pelatihan Internal HRD');

        $this->assertDatabaseHas('diklat', [
            'nama_kegiatan' => 'Pelatihan Internal HRD',
            'jenis_pelaksanaan' => 'internal',
        ]);
    }

    public function test_hrd_cannot_create_external_master_diklat(): void
    {
        $hrd = $this->createUserWithPegawai('hrd', '9600000000000002', 'HRD Create External');

        $response = $this->withTokenFor($hrd)->postJson('/api/hrd/diklat', [
            'nama_kegiatan' => 'Seminar Luar Kota',
            'kategori' => 'Teknis',
            'jenis_diklat' => 'Medis',
            'penyelenggara' => 'IDI',
            'lokasi' => 'Surabaya',
            'tanggal_mulai' => now()->addDays(10)->toDateString(),
            'tanggal_selesai' => now()->addDays(12)->toDateString(),
            'jp' => 20,
            'jenis_pelaksana' => 'external',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validasi gagal.');

        $this->assertDatabaseMissing('diklat', [
            'nama_kegiatan' => 'Seminar Luar Kota',
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

    private function createUserWithPegawai(string $role, string $nik, string $nama): User
    {
        $user = User::query()->create([
            'username' => $nik,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);

        $unitKerja = UnitKerja::query()->firstOrCreate(['nama' => 'Unit Mutation HRD']);
        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama' => 'Jabatan Mutation HRD'],
            ['unit_kerja_id' => $unitKerja->id, 'tmt_mulai' => now()->toDateString()]
        );

        $pegawai = Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => $nik,
            'nama' => $nama,
            'jabatan_id' => $jabatan->id,
            'status_pegawai' => 'aktif',
            'tgl_masuk' => now()->subYear()->toDateString(),
        ]);

        PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'alamat' => 'Jl. Mutation HRD',
            'no_telp' => '081234567890',
            'email' => strtolower(str_replace(' ', '.', $nama)).'@example.test',
        ]);

        $user->setRelation('pegawai', $pegawai);

        return $user;
    }
}
