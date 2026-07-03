<?php

namespace Tests\Feature\Api;

use App\Models\Anak;
use App\Models\Pasangan;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\User;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PegawaiDetailKeluargaTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_detail_keluarga_endpoint_returns_detailed_structure(): void
    {
        $admin = $this->createPegawaiWithUser('3174010101010001', 'Admin SIMPEG', 'admin');
        $pegawai = $this->createPegawaiWithUser('3174010101010002', 'Pegawai Keluarga', 'pegawai');

        // Create PegawaiPribadi
        $pribadi = PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_kelamin' => 'L',
        ]);

        // Create Keluarga
        Pasangan::query()->create([
            'pegawai_pribadi_id' => $pribadi->id,
            'nama_lengkap' => 'Istri Pegawai',
            'status_pernikahan' => 'Istri',
            'buku_nikah_file_path' => 'dokumen/buku_nikah.pdf',
        ]);

        Anak::query()->create([
            'pegawai_pribadi_id' => $pribadi->id,
            'nama_lengkap' => 'Anak Pegawai',
            'akta_kelahiran_file_path' => 'dokumen/akta.pdf',
        ]);

        $token = app(JwtService::class)->generate([
            'sub' => $admin->user_id,
            'role' => 'admin',
            'pegawai_id' => $admin->id,
        ])['token'];

        $response = $this->getJson("/api/pegawai/{$pegawai->id}/keluarga", [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertOk()
            ->assertJsonPath('data.keluarga.total_keluarga', 2)
            ->assertJsonPath('data.keluarga.rincian.pasangan.0.nama_lengkap', 'Istri Pegawai')
            ->assertJsonPath('data.keluarga.rincian.pasangan.0.link_buku_nikah', '/dokumen/buku_nikah.pdf')
            ->assertJsonPath('data.keluarga.rincian.anak.0.nama_lengkap', 'Anak Pegawai')
            ->assertJsonPath('data.keluarga.rincian.anak.0.link_akta_kelahiran', '/dokumen/akta.pdf');
    }

    private function createPegawaiWithUser(string $nik, string $nama, string $role): Pegawai
    {
        $user = User::query()->create([
            'username' => $nik,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);

        return Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => $nik,
            'nama' => $nama,
            'status_pegawai' => 'aktif',
        ]);
    }
}
