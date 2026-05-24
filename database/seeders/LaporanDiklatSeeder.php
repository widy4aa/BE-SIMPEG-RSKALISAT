<?php

namespace Database\Seeders;

use App\Models\Diklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use App\Models\JenisDiklat;
use App\Models\JenisBiaya;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class LaporanDiklatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Check if we have Pegawai
        $pegawaiList = Pegawai::all();
        if ($pegawaiList->count() < 5) {
            $this->command->info("Data Pegawai kurang dari 5. Menjalankan PegawaiDummySeeder...");
            $this->call(PegawaiDummySeeder::class);
            $pegawaiList = Pegawai::all();
        }

        $jenisDiklatIds = JenisDiklat::pluck('id')->toArray();
        $jenisBiayaIds = JenisBiaya::pluck('id')->toArray();

        if (empty($jenisDiklatIds) || empty($jenisBiayaIds)) {
            $this->command->error("Master data (JenisDiklat, JenisBiaya) belum ada. Silakan seed master data terlebih dahulu.");
            return;
        }

        $this->command->info("Membuat data Diklat Internal untuk Laporan...");

        for ($i = 1; $i <= 10; $i++) {
            // Random date between Jan 2026 and May 2026
            $createdDate = Carbon::create(2026, $faker->numberBetween(1, 5), $faker->numberBetween(1, 28));
            
            $diklat = Diklat::create([
                'nama_kegiatan' => 'Diklat Internal ' . Str::title($faker->words(3, true)),
                'kategori_diklat_id' => null,
                'jenis_diklat_id' => $faker->randomElement($jenisDiklatIds),
                'penyelenggara' => 'RS Kalisat',
                'tempat' => 'Aula RS Kalisat',
                'tanggal_mulai' => $createdDate->copy()->addDays(5)->toDateString(),
                'tanggal_selesai' => $createdDate->copy()->addDays(7)->toDateString(),
                'waktu' => '08:00:00',
                'jp' => $faker->randomElement([8, 16, 24]),
                'jenis_biaya_id' => $faker->randomElement($jenisBiayaIds),
                'total_biaya' => $faker->randomElement([500000, 1000000, 1500000, 2000000]),
                'jenis_pelaksanaan' => 'internal',
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
            ]);

            // Select random pegawais (3 to 8 per diklat)
            $peserta = $pegawaiList->random(min($pegawaiList->count(), $faker->numberBetween(3, 8)));

            foreach ($peserta as $p) {
                // Determine random state for the participant
                // 1. Uploaded & Valid
                // 2. Uploaded & Tidak Valid
                // 3. Uploaded & Belum divalidasi
                // 4. Belum upload & Belum validasi
                $state = $faker->randomElement([1, 1, 1, 2, 3, 4]); // Higher chance for valid

                $sertifPath = null;
                $statusValidasi = null;

                if ($state === 1) {
                    $sertifPath = 'dokumen/sertif-diklat/dummy-' . Str::random(5) . '.pdf';
                    $statusValidasi = 'valid';
                } elseif ($state === 2) {
                    $sertifPath = 'dokumen/sertif-diklat/dummy-' . Str::random(5) . '.pdf';
                    $statusValidasi = 'tidak valid';
                } elseif ($state === 3) {
                    $sertifPath = 'dokumen/sertif-diklat/dummy-' . Str::random(5) . '.pdf';
                    $statusValidasi = null;
                } else {
                    $sertifPath = null;
                    $statusValidasi = null;
                }

                ListJadwalDiklat::create([
                    'diklat_id' => $diklat->id,
                    'pegawai_id' => $p->id,
                    'sertif_file_path' => $sertifPath,
                    'no_sertif' => $sertifPath ? 'SERTIF/' . Str::upper(Str::random(5)) : null,
                    'status_diklat' => 'sudah terlaksana',
                    'status_kelayakan' => 'layak',
                    'status_validasi' => $statusValidasi,
                ]);
            }
        }

        $this->command->info("Berhasil melakukan seeding 10 Diklat Internal dengan variasi data partisipan!");
    }
}
