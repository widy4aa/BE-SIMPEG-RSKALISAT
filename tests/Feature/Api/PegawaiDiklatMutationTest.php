<?php

namespace Tests\Feature\Api;

use App\Models\Diklat;
use App\Models\Jabatan;
use App\Models\JenisDiklat;
use App\Models\KategoriDiklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\UnitKerja;
use App\Models\User;
use App\Services\Diklat\PegawaiDiklatFileService;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PegawaiDiklatMutationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mock(PegawaiDiklatFileService::class, function ($mock) {
            $mock->shouldReceive('storeSertifikat')->andReturn('dokumen/sertif-diklat/dummy.pdf');
        });
    }

    public function test_pegawai_can_create_finished_internal_diklat(): void
    {
        $user = $this->createUserWithPegawai('pegawai', '9700000000000001', 'Pegawai Create Diklat');

        $response = $this->withTokenFor($user)->postJson('/api/diklat', $this->diklatPayload([
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->subDay()->toDateString(),
            'jenis_pelaksana' => 'internal',
            'jenis_biaya' => 'Mandiri',
            'total_biaya' => 250000,
        ]));

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Diklat berhasil dibuat.')
            ->assertJsonPath('data.status_diklat', 'sudah terlaksana')
            ->assertJsonPath('data.status_kelayakan', 'layak')
            ->assertJsonPath('data.status_validasi', null);

        $this->assertDatabaseHas('diklat', [
            'nama_kegiatan' => 'Pelatihan Mutation Test',
            'jenis_pelaksanaan' => 'internal',
            'total_biaya' => 250000,
        ]);

        $this->assertDatabaseHas('list_jadwal_diklat', [
            'pegawai_id' => $user->pegawai->id,
            'status_diklat' => 'sudah terlaksana',
            'status_kelayakan' => 'layak',
            'status_validasi' => null,
        ]);
    }

    public function test_pegawai_cannot_create_diklat_that_has_not_finished(): void
    {
        $user = $this->createUserWithPegawai('pegawai', '9700000000000002', 'Pegawai Future Diklat');

        $response = $this->withTokenFor($user)->postJson('/api/diklat', $this->diklatPayload([
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
        ]));

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Pegawai hanya dapat menambahkan riwayat diklat mandiri yang sudah selesai dilaksanakan.');

        $this->assertDatabaseMissing('diklat', [
            'nama_kegiatan' => 'Pelatihan Mutation Test',
        ]);
    }

    public function test_pegawai_cannot_delete_diklat_that_is_already_valid_or_layak(): void
    {
        $user = $this->createUserWithPegawai('pegawai', '9700000000000003', 'Pegawai Delete Diklat');
        $diklat = $this->createOwnedDiklat($user->pegawai, jenisPelaksanaan: 'external');
        $jadwal = ListJadwalDiklat::query()->where('diklat_id', $diklat->id)->firstOrFail();
        $jadwal->update(['status_validasi' => 'valid']);

        $response = $this->withTokenFor($user)->deleteJson("/api/diklat/{$diklat->id}");

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Diklat tidak bisa dihapus karena sudah masuk kelayakan atau sudah validasi.');

        $this->assertDatabaseHas('diklat', [
            'id' => $diklat->id,
            'deleted_at' => null,
        ]);
    }

    public function test_pegawai_cannot_edit_external_diklat_that_has_been_seen_by_hrd(): void
    {
        $user = $this->createUserWithPegawai('pegawai', '9700000000000010', 'Pegawai Edit External Diklat');
        $diklat = $this->createOwnedDiklat($user->pegawai, jenisPelaksanaan: 'external');
        $jadwal = ListJadwalDiklat::query()->where('diklat_id', $diklat->id)->firstOrFail();
        $jadwal->update(['status_kelayakan' => 'layak']);

        $response = $this->withTokenFor($user)->patchJson("/api/diklat/{$diklat->id}", [
            'nama_kegiatan' => 'External Update Ditolak',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Diklat sudah dilihat oleh HRD, sehingga diklat external tidak bisa diedit lagi.');
    }

    public function test_upload_laporan_internal_resets_validation_status(): void
    {
        $user = $this->createUserWithPegawai('pegawai', '9700000000000004', 'Pegawai Upload Laporan');
        $diklat = $this->createOwnedDiklat($user->pegawai, jenisPelaksanaan: 'internal');
        $jadwal = ListJadwalDiklat::query()->where('diklat_id', $diklat->id)->firstOrFail();
        $jadwal->update(['status_validasi' => 'tidak valid']);

        $response = $this->withTokenFor($user)->postJson("/api/diklat/{$diklat->id}/upload-laporan", [
            'no_sertif' => 'CERT-RESET-001',
            'upload_laporan' => UploadedFile::fake()->create('laporan.pdf', 100, 'application/pdf'),
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Laporan berhasil diupload/diedit.')
            ->assertJsonPath('data.no_sertif', 'CERT-RESET-001')
            ->assertJsonPath('data.status_validasi', null);

        $this->assertDatabaseHas('list_jadwal_diklat', [
            'id' => $jadwal->id,
            'no_sertif' => 'CERT-RESET-001',
            'status_validasi' => null,
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

        $unitKerja = UnitKerja::query()->firstOrCreate(['nama' => 'Unit Mutation']);
        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama' => 'Jabatan Mutation'],
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
            'alamat' => 'Jl. Mutation',
            'no_telp' => '081234567890',
            'email' => strtolower(str_replace(' ', '.', $nama)).'@example.test',
        ]);

        $user->setRelation('pegawai', $pegawai);

        return $user;
    }

    private function diklatPayload(array $overrides = []): array
    {
        return array_merge([
            'nama_kegiatan' => 'Pelatihan Mutation Test',
            'kategori' => 'Teknis Mutation',
            'jenis_diklat' => 'ASN Mutation',
            'penyelenggara' => 'RS Kalisat',
            'lokasi' => 'Aula Mutation',
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->subDay()->toDateString(),
            'no_sertif' => 'CERT-MUT-001',
            'upload_sertif' => UploadedFile::fake()->create('sertif.pdf', 100, 'application/pdf'),
            'jp' => 8,
            'jenis_biaya' => 'Mandiri',
            'total_biaya' => 250000,
            'catatan' => 'Catatan mutation',
            'jenis_pelaksana' => 'internal',
            'waktu' => '08:00:00',
        ], $overrides);
    }

    private function createOwnedDiklat(Pegawai $pegawai, string $jenisPelaksanaan): Diklat
    {
        $jenis = JenisDiklat::query()->create(['nama' => 'Jenis Mutation '.$jenisPelaksanaan]);
        $kategori = KategoriDiklat::query()->create(['nama' => 'Kategori Mutation '.$jenisPelaksanaan]);

        $diklat = Diklat::query()->create([
            'jenis_diklat_id' => $jenis->id,
            'kategori_diklat_id' => $kategori->id,
            'created_by' => $pegawai->id,
            'nama_kegiatan' => 'Owned Mutation '.$jenisPelaksanaan,
            'penyelenggara' => 'RS Kalisat',
            'tanggal_mulai' => now()->subDays(5)->toDateString(),
            'tanggal_selesai' => now()->subDay()->toDateString(),
            'tempat' => 'Aula Mutation',
            'waktu' => '08:00:00',
            'jp' => 8,
            'jenis_pelaksanaan' => $jenisPelaksanaan,
        ]);

        ListJadwalDiklat::query()->create([
            'diklat_id' => $diklat->id,
            'pegawai_id' => $pegawai->id,
            'status_diklat' => 'sudah terlaksana',
            'status_kelayakan' => $jenisPelaksanaan === 'internal' ? 'layak' : null,
            'status_validasi' => null,
        ]);

        return $diklat;
    }
}
