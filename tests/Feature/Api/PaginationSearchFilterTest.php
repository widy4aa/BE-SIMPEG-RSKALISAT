<?php

namespace Tests\Feature\Api;

use App\Models\Diklat;
use App\Models\Jabatan;
use App\Models\JenisDiklat;
use App\Models\JenisPegawai;
use App\Models\KategoriDiklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\Profesi;
use App\Models\UnitKerja;
use App\Models\User;
use App\Services\Security\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaginationSearchFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_pegawai_index_supports_search_filters_per_page_and_role_counts(): void
    {
        $admin = $this->createUserWithPegawai('admin', '9100000000000001', 'Admin Filter');
        $this->createUserWithPegawai('hrd', '9100000000000002', 'HRD Filter');
        $this->createUserWithPegawai('direktur', '9100000000000003', 'Direktur Filter');

        $dokter = Profesi::query()->create(['nama' => 'Dokter Umum']);
        $perawat = Profesi::query()->create(['nama' => 'Perawat']);
        $pns = JenisPegawai::query()->create(['nama' => 'PNS']);
        $kontrak = JenisPegawai::query()->create(['nama' => 'Pegawai Kontrak']);

        $match = $this->createUserWithPegawai(
            role: 'pegawai',
            nik: '9200000000000001',
            nama: 'Budi Filter Match',
            profesi: $dokter,
            jenisPegawai: $pns,
            pendidikan: 'S1/D4',
            statusPegawai: 'aktif',
            complete: true
        );

        $this->createUserWithPegawai(
            role: 'pegawai',
            nik: '9200000000000002',
            nama: 'Siti Filter Other',
            profesi: $perawat,
            jenisPegawai: $kontrak,
            pendidikan: 'D3',
            statusPegawai: 'tidak aktif',
            complete: false
        );

        $response = $this->withTokenFor($admin)
            ->getJson('/api/pegawai?page=1&per_page=1&search=Dokter&status_kelengkapan=lengkap&jenis_pegawai=PNS&pendidikan=S1&status_pegawai=aktif&profesi=Dokter');

        $response->assertOk()
            ->assertJsonPath('data.jumlah_admin', 1)
            ->assertJsonPath('data.jumlah_hrd', 1)
            ->assertJsonPath('data.jumlah_direktur', 1)
            ->assertJsonPath('data.pegawai.per_page', 1)
            ->assertJsonPath('data.pegawai.total', 1)
            ->assertJsonPath('data.pegawai.data.0.id_pegawai', $match->pegawai->id)
            ->assertJsonPath('data.pegawai.data.0.role', 'pegawai');
    }

    public function test_diklat_pegawai_index_supports_pagination_search_jenis_and_status_filters(): void
    {
        $pegawaiUser = $this->createUserWithPegawai('pegawai', '9300000000000001', 'Pegawai Diklat');
        $asn = JenisDiklat::query()->create(['nama' => 'ASN']);
        $tenagaKesehatan = JenisDiklat::query()->create(['nama' => 'Tenaga Kesehatan']);
        $kategori = KategoriDiklat::query()->create(['nama' => 'Teknis']);

        $matching = $this->createDiklat(
            nama: 'Pelatihan Filter Berlangsung',
            penyelenggara: 'RS Kalisat',
            jenis: $asn,
            kategori: $kategori,
            mulai: now()->subDay()->toDateString(),
            selesai: now()->addDay()->toDateString(),
            pegawai: $pegawaiUser->pegawai
        );

        $this->createDiklat(
            nama: 'Workshop Lain',
            penyelenggara: 'Vendor',
            jenis: $tenagaKesehatan,
            kategori: $kategori,
            mulai: now()->addDays(5)->toDateString(),
            selesai: now()->addDays(7)->toDateString(),
            pegawai: $pegawaiUser->pegawai
        );

        $response = $this->withTokenFor($pegawaiUser)
            ->getJson('/api/diklat?page=1&per_page=1&search=Filter&jenis=ASN&status=berlangsung');

        $response->assertOk()
            ->assertJsonPath('data.diklat.riwayat_diklat.per_page', 1)
            ->assertJsonPath('data.diklat.riwayat_diklat.total', 1)
            ->assertJsonPath('data.diklat.riwayat_diklat.data.0.id', $matching->id)
            ->assertJsonPath('data.diklat.riwayat_diklat.data.0.status', 'berlangsung')
            ->assertJsonPath('data.diklat.riwayat_diklat.data.0.uploadlaporan', true);
    }

    public function test_diklat_all_supports_pagination_search_and_jenis_filters(): void
    {
        $hrd = $this->createUserWithPegawai('hrd', '9400000000000001', 'HRD Diklat');
        $asn = JenisDiklat::query()->create(['nama' => 'ASN']);
        $tenagaKesehatan = JenisDiklat::query()->create(['nama' => 'Tenaga Kesehatan']);
        $kategori = KategoriDiklat::query()->create(['nama' => 'Manajerial']);

        $matching = $this->createDiklat(
            nama: 'Master Filter ASN',
            penyelenggara: 'BPSDM',
            jenis: $asn,
            kategori: $kategori,
            mulai: now()->addDays(10)->toDateString(),
            selesai: now()->addDays(12)->toDateString(),
            pegawai: $hrd->pegawai
        );

        $this->createDiklat(
            nama: 'Master Tenkes',
            penyelenggara: 'Komite',
            jenis: $tenagaKesehatan,
            kategori: $kategori,
            mulai: now()->addDays(10)->toDateString(),
            selesai: now()->addDays(12)->toDateString(),
            pegawai: $hrd->pegawai
        );

        $response = $this->withTokenFor($hrd)
            ->getJson('/api/diklat/all?page=1&per_page=1&search=BPSDM&jenis=ASN');

        $response->assertOk()
            ->assertJsonPath('data.per_page', 1)
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.data.0.id_diklat', $matching->id)
            ->assertJsonPath('data.data.0.jenis', 'ASN');
    }

    public function test_external_diklat_cannot_be_approved_for_kelayakan_before_laporan_is_uploaded(): void
    {
        $hrd = $this->createUserWithPegawai('hrd', '9500000000000001', 'HRD Kelayakan');
        $jenis = JenisDiklat::query()->create(['nama' => 'External']);
        $kategori = KategoriDiklat::query()->create(['nama' => 'Teknis']);
        $diklat = $this->createDiklat(
            nama: 'External Tanpa Laporan',
            penyelenggara: 'Vendor',
            jenis: $jenis,
            kategori: $kategori,
            mulai: now()->addDays(2)->toDateString(),
            selesai: now()->addDays(3)->toDateString(),
            pegawai: $hrd->pegawai,
            jenisPelaksanaan: 'external'
        );
        $jadwal = ListJadwalDiklat::query()->where('diklat_id', $diklat->id)->firstOrFail();

        $response = $this->withTokenFor($hrd)
            ->patchJson("/api/hrd/diklat/{$jadwal->id}/status/layak", [
                'status_kelayakan' => true,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'belum upload laporan');
    }

    public function test_internal_diklat_cannot_be_approved_for_validasi_before_laporan_is_uploaded(): void
    {
        $hrd = $this->createUserWithPegawai('hrd', '9600000000000001', 'HRD Validasi');
        $jenis = JenisDiklat::query()->create(['nama' => 'Internal']);
        $kategori = KategoriDiklat::query()->create(['nama' => 'Teknis']);
        $diklat = $this->createDiklat(
            nama: 'Internal Tanpa Laporan',
            penyelenggara: 'RS Kalisat',
            jenis: $jenis,
            kategori: $kategori,
            mulai: now()->subDay()->toDateString(),
            selesai: now()->addDay()->toDateString(),
            pegawai: $hrd->pegawai,
            jenisPelaksanaan: 'internal'
        );
        $jadwal = ListJadwalDiklat::query()->where('diklat_id', $diklat->id)->firstOrFail();

        $response = $this->withTokenFor($hrd)
            ->patchJson("/api/hrd/diklat/{$jadwal->id}/status/validasi", [
                'status_validasi' => true,
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'belum upload laporan');
    }


    private function withTokenFor(User $user): self
    {
        $token = app(JwtService::class)->generate([
            'sub' => (string) $user->id,
            'role' => $user->role,
        ])['token'];

        return $this->withHeader('Authorization', 'Bearer '.$token);
    }

    private function createUserWithPegawai(
        string $role,
        string $nik,
        string $nama,
        ?Profesi $profesi = null,
        ?JenisPegawai $jenisPegawai = null,
        ?string $pendidikan = null,
        string $statusPegawai = 'aktif',
        bool $complete = true
    ): User {
        $user = User::query()->create([
            'username' => $nik,
            'password' => Hash::make('password'),
            'role' => $role,
            'is_active' => true,
        ]);

        $unitKerja = UnitKerja::query()->firstOrCreate(['nama' => 'Unit Filter']);
        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama' => 'Jabatan Filter'],
            ['unit_kerja_id' => $unitKerja->id, 'tmt_mulai' => now()->toDateString()]
        );

        $pegawai = Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => $nik,
            'nama' => $nama,
            'jenis_pegawai_id' => $jenisPegawai?->id,
            'profesi_id' => $profesi?->id,
            'jabatan_id' => $jabatan->id,
            'status_pegawai' => $statusPegawai,
            'tgl_masuk' => $complete ? now()->subYear()->toDateString() : null,
        ]);

        PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'pendidikan_terakhir' => $pendidikan,
            'tanggal_lahir' => $complete ? '1990-01-01' : null,
            'jenis_kelamin' => $complete ? 'L' : null,
            'agama' => $complete ? 'Islam' : null,
            'alamat' => $complete ? 'Jl. Filter' : null,
            'no_telp' => $complete ? '081234567890' : null,
            'email' => strtolower(str_replace(' ', '.', $nama)).'@example.test',
        ]);

        $user->setRelation('pegawai', $pegawai);

        return $user;
    }

    private function createDiklat(
        string $nama,
        string $penyelenggara,
        JenisDiklat $jenis,
        KategoriDiklat $kategori,
        string $mulai,
        string $selesai,
        Pegawai $pegawai,
        string $jenisPelaksanaan = 'internal'
    ): Diklat {
        $diklat = Diklat::query()->create([
            'jenis_diklat_id' => $jenis->id,
            'kategori_diklat_id' => $kategori->id,
            'created_by' => $pegawai->id,
            'nama_kegiatan' => $nama,
            'penyelenggara' => $penyelenggara,
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'tempat' => 'Aula',
            'waktu' => '08:00:00',
            'jp' => 8,
            'jenis_pelaksanaan' => $jenisPelaksanaan,
        ]);

        ListJadwalDiklat::query()->create([
            'diklat_id' => $diklat->id,
            'pegawai_id' => $pegawai->id,
            'status_diklat' => 'sedang terlaksana',
        ]);

        return $diklat;
    }
}
