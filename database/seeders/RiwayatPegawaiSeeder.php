<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\JenisSip;
use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\Pegawai;
use App\Models\Pendidikan;
use App\Models\PenugasanKlinis;
use App\Models\Sip;
use App\Models\StrPegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RiwayatPegawaiSeeder extends Seeder
{
    /**
     * Seed riwayat pegawai: pendidikan, jabatan, pangkat, STR, SIP, penugasan klinis.
     */
    public function run(): void
    {
        $pegawai = Pegawai::query()
            ->where('nik', '3174010101010001')
            ->with('pribadi')
            ->first();

        if ($pegawai === null || $pegawai->pribadi === null) {
            return;
        }

        $jabatanStaf = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf Kepegawaian'],
            ['tmt_mulai' => '2020-01-01']
        );

        $jabatanKoordinator = Jabatan::query()->firstOrCreate(
            ['nama' => 'Koordinator Administrasi SDM'],
            ['tmt_mulai' => '2024-01-01']
        );

        $pangkatPenataMuda = Pangkat::query()->firstOrCreate(['nama' => 'Penata Muda']);
        $pangkatPenataMudaTk1 = Pangkat::query()->firstOrCreate(['nama' => 'Penata Muda Tingkat I']);

        $jenisSipRs = JenisSip::query()->firstOrCreate(['nama' => 'SIP Praktik Rumah Sakit']);

        Pendidikan::query()->where('pegawai_pribadi_id', $pegawai->pribadi->id)->delete();
        Pendidikan::query()->create([
            'pegawai_pribadi_id' => $pegawai->pribadi->id,
            'jenjang' => 'D3',
            'institusi' => 'Poltekkes Kemenkes Malang',
            'jurusan' => 'Administrasi Kesehatan',
            'tahun_lulus' => 2011,
            'nomor_ijazah' => 'IJZ-D3-2011-001',
            'ijazah_file_path' => 'dokumen/ijazah/budi-d3.pdf',
        ]);

        Pendidikan::query()->create([
            'pegawai_pribadi_id' => $pegawai->pribadi->id,
            'jenjang' => 'S1/D4',
            'institusi' => 'Universitas Jember',
            'jurusan' => 'Manajemen SDM',
            'tahun_lulus' => 2015,
            'nomor_ijazah' => 'IJZ-S1-2015-007',
            'ijazah_file_path' => 'dokumen/ijazah/budi-s1.pdf',
        ]);

        JabatanPegawai::query()->where('pegawai_id', $pegawai->id)->delete();
        JabatanPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'jabatan_id' => $jabatanStaf->id,
            'is_current' => false,
            'started_at' => '2020-01-01',
            'ended_at' => '2023-12-31',
            'note' => 'Riwayat jabatan awal.',
        ]);

        JabatanPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'jabatan_id' => $jabatanKoordinator->id,
            'is_current' => true,
            'started_at' => '2024-01-01',
            'ended_at' => null,
            'note' => 'Jabatan aktif saat ini.',
        ]);

        PangkatPegawai::query()->where('pegawai_id', $pegawai->id)->delete();
        PangkatPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'pangkat_id' => $pangkatPenataMuda->id,
            'is_current' => false,
            'started_at' => '2020-01-01',
            'ended_at' => '2023-12-31',
            'note' => 'Riwayat pangkat awal.',
        ]);

        PangkatPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'pangkat_id' => $pangkatPenataMudaTk1->id,
            'is_current' => true,
            'started_at' => '2024-01-01',
            'ended_at' => null,
            'note' => 'Pangkat aktif saat ini.',
        ]);

        StrPegawai::query()->where('pegawai_id', $pegawai->id)->delete();
        $strLama = [
            'pegawai_id' => $pegawai->id,
            'nomor_str' => 'STR-3174010101010001-OLD',
            'tanggal_terbit' => '2019-01-01',
            'tanggal_kadaluarsa' => '2023-12-31',
            'sk_file_path' => 'dokumen/str/budi-str-2019.pdf',
        ];
        if (Schema::hasColumn('str', 'is_current')) {
            $strLama['is_current'] = false;
        }
        StrPegawai::query()->create($strLama);

        $strBaru = [
            'pegawai_id' => $pegawai->id,
            'nomor_str' => 'STR-3174010101010001-NEW',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => '2028-12-31',
            'sk_file_path' => 'dokumen/str/budi-str-2024.pdf',
        ];
        if (Schema::hasColumn('str', 'is_current')) {
            $strBaru['is_current'] = true;
        }
        StrPegawai::query()->create($strBaru);

        Sip::query()->where('pegawai_id', $pegawai->id)->delete();
        $sipLama = [
            'pegawai_id' => $pegawai->id,
            'jenis_sip_id' => $jenisSipRs->id,
            'nomor_sip' => 'SIP-3174010101010001-OLD',
            'tanggal_terbit' => '2021-01-01',
            'tanggal_kadaluarsa' => '2023-12-31',
            'sk_file_path' => 'dokumen/sip/budi-sip-2021.pdf',
        ];
        if (Schema::hasColumn('sip', 'is_current')) {
            $sipLama['is_current'] = false;
        }
        Sip::query()->create($sipLama);

        $sipBaru = [
            'pegawai_id' => $pegawai->id,
            'jenis_sip_id' => $jenisSipRs->id,
            'nomor_sip' => 'SIP-3174010101010001-NEW',
            'tanggal_terbit' => '2024-01-01',
            'tanggal_kadaluarsa' => '2028-12-31',
            'sk_file_path' => 'dokumen/sip/budi-sip-2024.pdf',
        ];
        if (Schema::hasColumn('sip', 'is_current')) {
            $sipBaru['is_current'] = true;
        }
        Sip::query()->create($sipBaru);

        PenugasanKlinis::query()->where('pegawai_id', $pegawai->id)->delete();
        $penugasanLama = [
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'SK-KLINIS-BUDI-2022',
            'tgl_mulai' => '2022-01-01',
            'tgl_kadaluarsa' => '2023-12-31',
            'dokumen_file_path' => 'dokumen/penugasan/budi-penugasan-2022.pdf',
        ];
        if (Schema::hasColumn('penugasan_klinis', 'is_current')) {
            $penugasanLama['is_current'] = false;
        }
        PenugasanKlinis::query()->create($penugasanLama);

        $penugasanBaru = [
            'pegawai_id' => $pegawai->id,
            'nomor_surat' => 'SK-KLINIS-BUDI-2024',
            'tgl_mulai' => '2024-01-01',
            'tgl_kadaluarsa' => '2026-12-31',
            'dokumen_file_path' => 'dokumen/penugasan/budi-penugasan-2024.pdf',
        ];
        if (Schema::hasColumn('penugasan_klinis', 'is_current')) {
            $penugasanBaru['is_current'] = true;
        }
        PenugasanKlinis::query()->create($penugasanBaru);
    }
}
