<?php

namespace Tests\Feature\Generate;

use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\JenisSip;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\PenugasanKlinis;
use App\Models\Sip;
use App\Models\StrPegawai;
use App\Models\UnitKerja;
use App\Models\User;
use App\Services\Generate\CvService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CvServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    /**
     * Regression: the `is_current` columns were dropped from jabatan_pegawai,
     * str, sip, and penugasan_klinis. CvRepository uses raw SQL (stdClass), so
     * the model accessors never fire and CvService must compute the flag from
     * the date ranges instead of reading a (now missing) column.
     */
    public function test_generate_cv_data_computes_is_current_from_date_ranges(): void
    {
        Carbon::setTestNow('2026-07-02');

        $pegawai = $this->seedPegawaiWithRiwayat();

        $service = app(CvService::class);

        $data = $service->generateCvData($pegawai->user_id, 'pegawai');

        // Riwayat jabatan: open-ended period -> current, closed past period -> not current.
        $this->assertCount(2, $data['riwayat_jabatan']);
        $current = collect($data['riwayat_jabatan'])->firstWhere('tanggal_selesai', '-');
        $past = collect($data['riwayat_jabatan'])->firstWhere('tanggal_selesai', '2024-12-31');
        $this->assertTrue($current['is_current']);
        $this->assertFalse($past['is_current']);

        // STR still valid today.
        $this->assertCount(1, $data['str']);
        $this->assertTrue($data['str'][0]['is_current']);

        // SIP already expired.
        $this->assertCount(1, $data['sip']);
        $this->assertFalse($data['sip'][0]['is_current']);

        // Penugasan klinis valid today.
        $this->assertCount(1, $data['penugasan_klinis']);
        $this->assertTrue($data['penugasan_klinis'][0]['is_current']);
    }

    public function test_generate_cv_data_handles_pegawai_without_riwayat(): void
    {
        Carbon::setTestNow('2026-07-02');

        $user = User::query()->create([
            'username' => '3200000000000001',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'is_active' => true,
        ]);

        $pegawai = Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => '3200000000000001',
            'nama' => 'Pegawai Tanpa Riwayat',
            'status_pegawai' => 'aktif',
        ]);

        $data = app(CvService::class)->generateCvData($pegawai->user_id, 'pegawai');

        $this->assertSame([], $data['riwayat_jabatan']);
        $this->assertSame([], $data['str']);
        $this->assertSame([], $data['sip']);
        $this->assertSame([], $data['penugasan_klinis']);
        $this->assertSame('Pegawai Tanpa Riwayat', $data['header']['nama']);
    }

    private function seedPegawaiWithRiwayat(): Pegawai
    {
        $user = User::query()->create([
            'username' => '3100000000000001',
            'password' => bcrypt('password'),
            'role' => 'pegawai',
            'is_active' => true,
        ]);

        $pegawai = Pegawai::query()->create([
            'user_id' => $user->id,
            'nik' => '3100000000000001',
            'nama' => 'Budi Santoso',
            'status_pegawai' => 'aktif',
            'tgl_masuk' => '2020-01-01',
        ]);

        PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawai->id,
            'alamat' => 'Jl. Merdeka No. 1',
            'no_telp' => '08123456789',
            'tanggal_lahir' => '1990-05-05',
            'jenis_kelamin' => 'L',
        ]);

        $unitKerja = UnitKerja::query()->create(['nama' => 'Unit Rawat Inap']);
        $jabatan = Jabatan::query()->create([
            'unit_kerja_id' => $unitKerja->id,
            'nama' => 'Perawat',
        ]);

        // Current jabatan (no end date).
        JabatanPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'jabatan_id' => $jabatan->id,
            'started_at' => '2023-01-01',
            'ended_at' => null,
        ]);

        // Past jabatan (ended).
        JabatanPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'jabatan_id' => $jabatan->id,
            'started_at' => '2020-01-01',
            'ended_at' => '2024-12-31',
        ]);

        StrPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'nomor_str' => 'STR-001',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => '2029-01-01',
        ]);

        $jenisSip = JenisSip::query()->create(['nama' => 'SIP Perawat']);
        Sip::query()->create([
            'pegawai_id' => $pegawai->id,
            'jenis_sip_id' => $jenisSip->id,
            'nomor_sip' => 'SIP-001',
            'tanggal_terbit' => '2020-01-01',
            'tanggal_kadaluarsa' => '2023-01-01',
        ]);

        PenugasanKlinis::query()->create([
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'PK-001',
            'tgl_mulai' => '2024-01-01',
            'tgl_kadaluarsa' => '2027-01-01',
        ]);

        return $pegawai;
    }
}
