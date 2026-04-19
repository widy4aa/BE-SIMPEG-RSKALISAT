<?php

namespace Database\Seeders;

use App\Models\Diklat;
use App\Models\JenisDiklat;
use App\Models\JenisBiaya;
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
                'jp' => 24,
                'total_biaya' => 3500000,
                'jenis_biaya' => 'BLUD',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Program orientasi kompetensi manajemen SDM dasar.',
                'sertif_file_path' => null,
                'no_sertif' => null,
                'uploaded_at' => null,
            ],
            [
                'nama_kegiatan' => 'Workshop Pelayanan Prima',
                'status_diklat' => 'belum terlaksana',
                'tanggal_mulai' => '2026-07-05',
                'tanggal_selesai' => '2026-07-05',
                'tempat' => 'Ruang Diklat Lt.2',
                'waktu' => '09:00:00',
                'jp' => 8,
                'total_biaya' => 1750000,
                'jenis_biaya' => 'Mandiri',
                'jenis_pelaksanaan' => 'internal',
                'catatan' => 'Workshop satu hari untuk peningkatan layanan frontliner.',
                'sertif_file_path' => null,
                'no_sertif' => null,
                'uploaded_at' => null,
            ],
            [
                'nama_kegiatan' => 'Diklat Digital Administrasi Kepegawaian',
                'status_diklat' => 'sedang terlaksana',
                'tanggal_mulai' => '2026-04-15',
                'tanggal_selesai' => '2026-04-20',
                'tempat' => 'Lab Komputer SDM',
                'waktu' => '08:30:00',
                'jp' => 40,
                'total_biaya' => 5250000,
                'jenis_biaya' => 'APBD',
                'jenis_pelaksanaan' => 'external',
                'catatan' => 'Fokus digitalisasi administrasi kepegawaian berbasis sistem.',
                'sertif_file_path' => null,
                'no_sertif' => null,
                'uploaded_at' => null,
            ],
            [
                'nama_kegiatan' => 'Pelatihan Audit Internal SDM',
                'status_diklat' => 'sudah terlaksana',
                'tanggal_mulai' => '2026-03-01',
                'tanggal_selesai' => '2026-03-03',
                'tempat' => 'Balai Diklat Kabupaten',
                'waktu' => '08:00:00',
                'jp' => 20,
                'total_biaya' => 2800000,
                'jenis_biaya' => 'Hibah',
                'jenis_pelaksanaan' => 'external',
                'catatan' => 'Materi audit dan tindak lanjut mutu internal SDM.',
                'sertif_file_path' => 'dokumen/sertif-diklat/budi-audit-internal.pdf',
                'no_sertif' => 'SERTIF/SDM/2026/0001',
                'uploaded_at' => now()->subDays(35),
            ],
        ];

        foreach ($diklatSeeds as $seed) {
            $jenisBiaya = JenisBiaya::query()->firstOrCreate([
                'nama' => $seed['jenis_biaya'],
            ]);

            $diklat = Diklat::query()->updateOrCreate(
                [
                    'nama_kegiatan' => $seed['nama_kegiatan'],
                ],
                [
                    'jenis_diklat_id' => $jenisDiklat->id,
                    'kategori_diklat_id' => $kategoriDiklat->id,
                    'created_by' => $pegawaiBudi->id,
                    'status_kelayakan' => 'layak',
                    'status_validasi' => 'valid',
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
                    'pegawai_id' => $pegawaiBudi->id,
                ],
                [
                    'status_diklat' => $seed['status_diklat'],
                    'sertif_file_path' => $seed['sertif_file_path'],
                    'no_sertif' => $seed['no_sertif'],
                    'uploaded_at' => $seed['uploaded_at'],
                ]
            );
        }
    }
}
