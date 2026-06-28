<?php

namespace Tests\Feature\Api;

use App\Models\Diklat;
use App\Models\JenisBiaya;
use App\Models\JenisDiklat;
use App\Models\KategoriDiklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use App\Models\User;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PegawaiDetailDiklatTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_detail_diklat_endpoint_includes_complete_diklat_fields(): void
    {
        $admin = $this->createPegawaiWithUser('3174010101010001', 'Admin SIMPEG', 'admin');
        $pegawai = $this->createPegawaiWithUser('3174010101010002', 'Pegawai Diklat', 'pegawai');

        $jenis = JenisDiklat::query()->create(['nama' => 'Tenkes']);
        $kategori = KategoriDiklat::query()->create(['nama' => 'Teknis']);
        $jenisBiaya = JenisBiaya::query()->create(['nama' => 'APBD']);

        $diklat = Diklat::query()->create([
            'jenis_diklat_id' => $jenis->id,
            'kategori_diklat_id' => $kategori->id,
            'created_by' => $admin->id,
            'nama_kegiatan' => 'Pelatihan Keselamatan Pasien',
            'penyelenggara' => 'RS Kalisat',
            'tanggal_mulai' => '2026-04-21',
            'tanggal_selesai' => '2026-04-22',
            'tempat' => 'Aula RS Kalisat',
            'waktu' => '08:30:00',
            'jp' => 12,
            'total_biaya' => 1500000,
            'jenis_biaya_id' => $jenisBiaya->id,
            'jenis_pelaksanaan' => 'internal',
            'catatan' => 'Wajib membawa laptop.',
        ]);

        ListJadwalDiklat::query()->create([
            'diklat_id' => $diklat->id,
            'pegawai_id' => $pegawai->id,
            'sertif_file_path' => 'dokumen/sertif-diklat/sertif-test.pdf',
            'no_sertif' => 'CERT-001',
            'status_diklat' => 'sudah terlaksana',
            'status_kelayakan' => 'layak',
            'status_validasi' => 'valid',
        ]);

        $ditolak = Diklat::query()->create([
            'jenis_diklat_id' => $jenis->id,
            'kategori_diklat_id' => $kategori->id,
            'created_by' => $admin->id,
            'nama_kegiatan' => 'Diklat Tidak Layak',
            'penyelenggara' => 'RS Kalisat',
        ]);

        ListJadwalDiklat::query()->create([
            'diklat_id' => $ditolak->id,
            'pegawai_id' => $pegawai->id,
            'status_diklat' => 'sudah terlaksana',
            'status_kelayakan' => 'tidak layak',
        ]);

        $token = app(JwtService::class)->generate([
            'sub' => $admin->user_id,
            'role' => 'admin',
            'pegawai_id' => $admin->id,
        ])['token'];

        $response = $this->getJson("/api/pegawai/{$pegawai->id}/diklat", [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertOk()
            ->assertJsonMissingPath('data.pegawai')
            ->assertJsonMissingPath('data.keluarga')
            ->assertJsonMissingPath('data.riwayat_karir')
            ->assertJsonPath('data.diklat.total', 1)
            ->assertJsonPath('data.diklat.data.0.waktu', '08:30:00')
            ->assertJsonPath('data.diklat.data.0.created_by', 'Admin SIMPEG')
            ->assertJsonPath('data.diklat.data.0.total_biaya', '1500000.00')
            ->assertJsonPath('data.diklat.data.0.jenis_biaya', 'APBD')
            ->assertJsonPath('data.diklat.data.0.jenis_pelaksana', 'internal')
            ->assertJsonPath('data.diklat.data.0.catatan', 'Wajib membawa laptop.')
            ->assertJsonPath('data.diklat.data.0.sertif', 'dokumen/sertif-diklat/sertif-test.pdf')
            ->assertJsonPath('data.diklat.data.0.no_sertif', 'CERT-001');

        $this->getJson("/api/pegawai/{$pegawai->id}/diklat?kelayakan=all&per_page=1", [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()
            ->assertJsonPath('data.diklat.total', 2)
            ->assertJsonPath('data.diklat.per_page', 1)
            ->assertJsonPath('data.diklat.last_page', 2);
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
