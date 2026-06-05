<?php

namespace Database\Seeders;

use App\Models\Diklat;
use App\Models\JenisBiaya;
use App\Models\JenisDiklat;
use App\Models\KategoriDiklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class DiklatPegawaiBudiSeeder extends Seeder
{
    /**
     * Seed data diklat yang siap dipakai untuk pengujian filter dan laporan.
     */
    public function run(): void
    {
        $pegawaiByNik = Pegawai::query()
            ->whereIn('nik', [
                '3174010101010001',
                '3174010101010003',
                '3174010101010098',
                '3174010101010099',
            ])
            ->get()
            ->keyBy('nik');

        $pegawaiBudi = $pegawaiByNik->get('3174010101010001');

        if ($pegawaiBudi === null) {
            return;
        }

        $jenisAsn = JenisDiklat::query()->firstOrCreate(['nama' => 'ASN']);
        $jenisTenkes = JenisDiklat::query()->firstOrCreate(['nama' => 'Tenkes']);
        $kategoriTeknis = KategoriDiklat::query()->firstOrCreate(['nama' => 'Teknis']);
        $kategoriFungsional = KategoriDiklat::query()->firstOrCreate(['nama' => 'Fungsional']);
        $kategoriStruktural = KategoriDiklat::query()->firstOrCreate(['nama' => 'Struktural']);

        $diklatSeeds = [
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010001',
                'nama_kegiatan' => 'Internal - Orientasi Pelayanan Pasien',
                'jenis_diklat_id' => $jenisTenkes->id,
                'kategori_diklat_id' => $kategoriTeknis->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(18)->toDateString(),
                'tanggal_selesai' => now()->subDays(16)->toDateString(),
                'tempat' => 'Aula RS Kalisat',
                'waktu' => '08:00:00',
                'jp' => 16,
                'total_biaya' => 2500000,
                'jenis_biaya' => 'BLUD',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Contoh internal selesai, laporan belum diunggah. Harus uploadlaporan true dan tidak bisa acc validasi.',
                'sertif_file_path' => null,
                'no_sertif' => null,
                'uploaded_at' => null,
                'status_kelayakan' => 'layak',
                'status_validasi' => null,
            ],
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010001',
                'nama_kegiatan' => 'Internal - Pelatihan Keselamatan Pasien',
                'jenis_diklat_id' => $jenisTenkes->id,
                'kategori_diklat_id' => $kategoriTeknis->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(12)->toDateString(),
                'tanggal_selesai' => now()->subDays(10)->toDateString(),
                'tempat' => 'Ruang Diklat Lt. 2',
                'waktu' => '09:00:00',
                'jp' => 12,
                'total_biaya' => 1800000,
                'jenis_biaya' => 'APBD',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Contoh internal lengkap dan sudah valid. Harus uploadlaporan false.',
                'sertif_file_path' => 'dokumen/sertif-diklat/budi-keselamatan-pasien.pdf',
                'no_sertif' => 'SERTIF/INT/RSK/2026/0001',
                'uploaded_at' => now()->subDays(9),
                'status_kelayakan' => 'layak',
                'status_validasi' => 'valid',
            ],
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010001',
                'nama_kegiatan' => 'Internal - Refreshment Rekam Medis',
                'jenis_diklat_id' => $jenisAsn->id,
                'kategori_diklat_id' => $kategoriFungsional->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(8)->toDateString(),
                'tanggal_selesai' => now()->subDays(7)->toDateString(),
                'tempat' => 'Lab Komputer SDM',
                'waktu' => '08:30:00',
                'jp' => 8,
                'total_biaya' => 1500000,
                'jenis_biaya' => 'Mandiri',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Contoh internal lengkap tapi ditolak. Harus uploadlaporan true sampai valid ulang.',
                'sertif_file_path' => 'dokumen/sertif-diklat/budi-rekam-medis.pdf',
                'no_sertif' => 'SERTIF/INT/RSK/2026/0002',
                'uploaded_at' => now()->subDays(6),
                'status_kelayakan' => 'layak',
                'status_validasi' => 'tidak valid',
            ],
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010001',
                'nama_kegiatan' => 'External - Seminar Mutu Rumah Sakit',
                'jenis_diklat_id' => $jenisTenkes->id,
                'kategori_diklat_id' => $kategoriStruktural->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(30)->toDateString(),
                'tanggal_selesai' => now()->subDays(28)->toDateString(),
                'tempat' => 'Balai Diklat Kabupaten Jember',
                'waktu' => '08:00:00',
                'jp' => 20,
                'total_biaya' => 3500000,
                'jenis_biaya' => 'Hibah',
                'jenis_pelaksanaan' => 'external',
                'catatan' => 'Contoh external belum lengkap. Harus uploadlaporan true dan tidak bisa acc kelayakan.',
                'sertif_file_path' => null,
                'no_sertif' => null,
                'uploaded_at' => null,
                'status_kelayakan' => null,
                'status_validasi' => null,
            ],
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010001',
                'nama_kegiatan' => 'External - Audit Internal SDM',
                'jenis_diklat_id' => $jenisAsn->id,
                'kategori_diklat_id' => $kategoriTeknis->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(45)->toDateString(),
                'tanggal_selesai' => now()->subDays(43)->toDateString(),
                'tempat' => 'Pusdiklat Provinsi',
                'waktu' => '08:00:00',
                'jp' => 24,
                'total_biaya' => 4200000,
                'jenis_biaya' => 'APBD',
                'jenis_pelaksanaan' => 'external',
                'catatan' => 'Contoh external lengkap dan sudah layak. Harus uploadlaporan false.',
                'sertif_file_path' => 'dokumen/sertif-diklat/budi-audit-internal.pdf',
                'no_sertif' => 'SERTIF/EXT/RSK/2026/0001',
                'uploaded_at' => now()->subDays(42),
                'status_kelayakan' => 'layak',
                'status_validasi' => null,
            ],
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010001',
                'nama_kegiatan' => 'Berlangsung - Digital Administrasi Kepegawaian',
                'jenis_diklat_id' => $jenisAsn->id,
                'kategori_diklat_id' => $kategoriTeknis->id,
                'status_diklat' => 'sedang terlaksana',
                'tanggal_mulai' => now()->subDay()->toDateString(),
                'tanggal_selesai' => now()->addDays(2)->toDateString(),
                'tempat' => 'Lab Komputer SDM',
                'waktu' => '13:00:00',
                'jp' => 32,
                'total_biaya' => 5000000,
                'jenis_biaya' => 'BLUD',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Contoh filter status berlangsung.',
                'sertif_file_path' => null,
                'no_sertif' => null,
                'uploaded_at' => null,
                'status_kelayakan' => 'layak',
                'status_validasi' => null,
            ],
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010001',
                'nama_kegiatan' => 'Mendatang - Workshop Pelayanan Prima',
                'jenis_diklat_id' => $jenisTenkes->id,
                'kategori_diklat_id' => $kategoriFungsional->id,
                'status_diklat' => 'belum terlaksana',
                'tanggal_mulai' => now()->addDays(14)->toDateString(),
                'tanggal_selesai' => now()->addDays(15)->toDateString(),
                'tempat' => 'Ruang Diklat Lt. 2',
                'waktu' => '09:00:00',
                'jp' => 10,
                'total_biaya' => 2000000,
                'jenis_biaya' => 'Mandiri',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Contoh filter status mendatang.',
                'sertif_file_path' => null,
                'no_sertif' => null,
                'uploaded_at' => null,
                'status_kelayakan' => null,
                'status_validasi' => null,
            ],
            [
                'creator_nik' => '3174010101010003',
                'peserta_nik' => '3174010101010003',
                'nama_kegiatan' => 'Direktur - Leadership Rumah Sakit',
                'jenis_diklat_id' => $jenisAsn->id,
                'kategori_diklat_id' => $kategoriStruktural->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(60)->toDateString(),
                'tanggal_selesai' => now()->subDays(58)->toDateString(),
                'tempat' => 'Jakarta',
                'waktu' => '08:00:00',
                'jp' => 30,
                'total_biaya' => 6500000,
                'jenis_biaya' => 'APBD',
                'jenis_pelaksanaan' => 'external',
                'catatan' => 'Data diklat direktur untuk cek profile dan dashboard.',
                'sertif_file_path' => 'dokumen/sertif-diklat/direktur-leadership.pdf',
                'no_sertif' => 'SERTIF/DIR/RSK/2026/0001',
                'uploaded_at' => now()->subDays(57),
                'status_kelayakan' => 'layak',
                'status_validasi' => null,
            ],
            [
                'creator_nik' => '3174010101010098',
                'peserta_nik' => '3174010101010098',
                'nama_kegiatan' => 'HRD - Manajemen Talenta ASN',
                'jenis_diklat_id' => $jenisAsn->id,
                'kategori_diklat_id' => $kategoriFungsional->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(25)->toDateString(),
                'tanggal_selesai' => now()->subDays(23)->toDateString(),
                'tempat' => 'Aula BKPSDM',
                'waktu' => '08:00:00',
                'jp' => 18,
                'total_biaya' => 3000000,
                'jenis_biaya' => 'BLUD',
                'jenis_pelaksanaan' => 'external',
                'catatan' => 'Data diklat HRD dengan laporan lengkap.',
                'sertif_file_path' => 'dokumen/sertif-diklat/hrd-manajemen-talenta.pdf',
                'no_sertif' => 'SERTIF/HRD/RSK/2026/0001',
                'uploaded_at' => now()->subDays(22),
                'status_kelayakan' => 'layak',
                'status_validasi' => null,
            ],
            [
                'creator_nik' => '3174010101010099',
                'peserta_nik' => '3174010101010099',
                'nama_kegiatan' => 'Admin - Keamanan Data SIMPEG',
                'jenis_diklat_id' => $jenisAsn->id,
                'kategori_diklat_id' => $kategoriTeknis->id,
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => now()->subDays(20)->toDateString(),
                'tanggal_selesai' => now()->subDays(19)->toDateString(),
                'tempat' => 'Ruang Server RS Kalisat',
                'waktu' => '10:00:00',
                'jp' => 8,
                'total_biaya' => 1200000,
                'jenis_biaya' => 'BLUD',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Data diklat admin dengan validasi lengkap.',
                'sertif_file_path' => 'dokumen/sertif-diklat/admin-keamanan-data.pdf',
                'no_sertif' => 'SERTIF/ADM/RSK/2026/0001',
                'uploaded_at' => now()->subDays(18),
                'status_kelayakan' => 'layak',
                'status_validasi' => 'valid',
            ],
        ];

        foreach ($diklatSeeds as $seed) {
            $creator = $pegawaiByNik->get($seed['creator_nik']) ?? $pegawaiBudi;
            $peserta = $pegawaiByNik->get($seed['peserta_nik']) ?? $pegawaiBudi;
            $jenisBiaya = JenisBiaya::query()->firstOrCreate(['nama' => $seed['jenis_biaya']]);

            $diklat = Diklat::query()->updateOrCreate(
                ['nama_kegiatan' => $seed['nama_kegiatan']],
                [
                    'jenis_diklat_id' => $seed['jenis_diklat_id'],
                    'kategori_diklat_id' => $seed['kategori_diklat_id'],
                    'created_by' => $creator->id,
                    'penyelenggara' => 'Bagian SDM RS Kalisat',
                    'tanggal_mulai' => $seed['tanggal_mulai'],
                    'tanggal_selesai' => $seed['tanggal_selesai'],
                    'tempat' => $seed['tempat'],
                    'waktu' => $seed['waktu'],
                    'jp' => $seed['jp'],
                    'total_biaya' => $seed['total_biaya'],
                    'jenis_biaya_id' => $jenisBiaya->id,
                    'jenis_pelaksanaan' => $seed['jenis_pelaksanaan'],
                    'catatan' => $seed['catatan'],
                ]
            );

            ListJadwalDiklat::query()->updateOrCreate(
                [
                    'diklat_id' => $diklat->id,
                    'pegawai_id' => $peserta->id,
                ],
                [
                    'status_diklat' => $seed['status_diklat'],
                    'sertif_file_path' => $seed['sertif_file_path'],
                    'no_sertif' => $seed['no_sertif'],
                    'uploaded_at' => $seed['uploaded_at'],
                    'status_kelayakan' => $seed['status_kelayakan'],
                    'status_validasi' => $seed['status_validasi'],
                ]
            );
        }
    }
}
