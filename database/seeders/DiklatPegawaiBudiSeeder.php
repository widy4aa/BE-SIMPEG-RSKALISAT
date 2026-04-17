<?php

namespace Database\Seeders;

use App\Models\Diklat;
use App\Models\JenisDiklat;
use App\Models\KategoriDiklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class DiklatPegawaiBudiSeeder extends Seeder
{
    /**
     * Seed data diklat untuk pegawai Budi Santoso.
     */
    public function run(): void
    {
        $pegawaiBudi = Pegawai::query()->where('nik', '3174010101010001')->first();

        if ($pegawaiBudi === null) {
            return;
        }

        $jenisDiklat = JenisDiklat::query()->firstOrCreate(['nama' => 'ASN']);
        $kategoriDiklat = KategoriDiklat::query()->firstOrCreate(['nama' => 'Teknis']);

        $diklatSeeds = [
            [
                'nama_kegiatan' => 'Diklat Manajemen SDM Dasar',
                'status_diklat' => 'belum terlaksana',
                'tanggal_mulai' => '2026-06-10',
                'tanggal_selesai' => '2026-06-12',
                'tempat' => 'Aula RS Kalisat',
                'waktu' => '08:00:00',
                'laporan_file_path' => null,
                'uploaded_at' => null,
            ],
            [
                'nama_kegiatan' => 'Workshop Pelayanan Prima',
                'status_diklat' => 'belum terlaksana',
                'tanggal_mulai' => '2026-07-05',
                'tanggal_selesai' => '2026-07-05',
                'tempat' => 'Ruang Diklat Lt.2',
                'waktu' => '09:00:00',
                'laporan_file_path' => null,
                'uploaded_at' => null,
            ],
            [
                'nama_kegiatan' => 'Diklat Digital Administrasi Kepegawaian',
                'status_diklat' => 'sedang terlaksana',
                'tanggal_mulai' => '2026-04-15',
                'tanggal_selesai' => '2026-04-20',
                'tempat' => 'Lab Komputer SDM',
                'waktu' => '08:30:00',
                'laporan_file_path' => null,
                'uploaded_at' => null,
            ],
            [
                'nama_kegiatan' => 'Pelatihan Audit Internal SDM',
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => '2026-03-01',
                'tanggal_selesai' => '2026-03-03',
                'tempat' => 'Balai Diklat Kabupaten',
                'waktu' => '08:00:00',
                'laporan_file_path' => 'dokumen/laporan-diklat/budi-audit-internal.pdf',
                'uploaded_at' => now()->subDays(35),
            ],
        ];

        foreach ($diklatSeeds as $seed) {
            $diklat = Diklat::query()->updateOrCreate(
                [
                    'nama_kegiatan' => $seed['nama_kegiatan'],
                ],
                [
                    'jenis_diklat_id' => $jenisDiklat->id,
                    'kategori_diklat_id' => $kategoriDiklat->id,
                    'status_kelayakan' => 'layak',
                    'status_validasi' => 'valid',
                    'penyelenggara' => 'Bagian SDM RS Kalisat',
                    'tanggal_mulai' => $seed['tanggal_mulai'],
                    'tanggal_selesai' => $seed['tanggal_selesai'],
                    'tempat' => $seed['tempat'],
                    'waktu' => $seed['waktu'],
                ]
            );

            ListJadwalDiklat::query()->updateOrCreate(
                [
                    'diklat_id' => $diklat->id,
                    'pegawai_id' => $pegawaiBudi->id,
                ],
                [
                    'status_diklat' => $seed['status_diklat'],
                    'laporan_file_path' => $seed['laporan_file_path'],
                    'uploaded_at' => $seed['uploaded_at'],
                ]
            );
        }
    }
}
