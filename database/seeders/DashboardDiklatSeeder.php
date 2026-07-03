<?php

namespace Database\Seeders;

use App\Models\Diklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use App\Models\JenisDiklat;
use App\Models\KategoriDiklat;
use App\Models\JenisBiaya;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DashboardDiklatSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Pastikan ada pegawai
        $pegawais = Pegawai::all();
        if ($pegawais->count() < 10) {
            $this->command->info("Data Pegawai kurang dari 10. Menjalankan PegawaiDummySeeder...");
            $this->call(PegawaiDummySeeder::class);
            $pegawais = Pegawai::all();
        }

        // Pastikan referensi ada
        $jenisDiklatIds = JenisDiklat::pluck('id')->toArray();
        $kategoriDiklatIds = KategoriDiklat::pluck('id')->toArray();
        $jenisBiayaIds = JenisBiaya::pluck('id')->toArray();

        if (empty($jenisDiklatIds) || empty($kategoriDiklatIds)) {
            $this->command->info("Master data belum lengkap, menjalankan MasterReferensiSeeder...");
            $this->call(MasterReferensiSeeder::class);
            $jenisDiklatIds = JenisDiklat::pluck('id')->toArray();
            $kategoriDiklatIds = KategoriDiklat::pluck('id')->toArray();
            $jenisBiayaIds = JenisBiaya::pluck('id')->toArray();
        }

        // Ambil ID dari database agar dinamis
        $asnId = JenisDiklat::where('nama', 'ASN')->value('id');
        $tenkesId = JenisDiklat::where('nama', 'Tenkes')->value('id');

        if (!$asnId || !$tenkesId) {
            $this->command->error("ID untuk ASN atau Tenkes tidak ditemukan! Pastikan tabel jenis_diklat sudah terisi dengan benar.");
            return;
        }

        $adminId = 1; // Asumsi admin user ID

        // Kita akan membuat 15 Diklat untuk ASN dan 15 Diklat untuk Tenkes
        $jenisDiklats = [
            $asnId => 'Diklat Kepemimpinan ASN',
            $tenkesId => 'Pelatihan Medis Tenkes'
        ];

        foreach ($jenisDiklats as $jenisDiklatId => $baseName) {
            for ($i = 0; $i < 15; $i++) {
                // Tentukan status waktu secara random: 0=Selesai, 1=Berlangsung, 2=Belum Terlaksana
                $statusWaktu = $faker->randomElement([0, 0, 0, 1, 1, 2]); // Lebih banyak yang sudah selesai

                $now = Carbon::now();
                if ($statusWaktu === 0) { // Selesai
                    $start = $now->copy()->subDays($faker->numberBetween(10, 60));
                    $end = $start->copy()->addDays($faker->numberBetween(1, 5));
                } elseif ($statusWaktu === 1) { // Berlangsung
                    $start = $now->copy()->subDays($faker->numberBetween(1, 3));
                    $end = $now->copy()->addDays($faker->numberBetween(1, 5));
                } else { // Belum Terlaksana
                    $start = $now->copy()->addDays($faker->numberBetween(5, 30));
                    $end = $start->copy()->addDays($faker->numberBetween(1, 5));
                }

                $diklat = Diklat::create([
                    'nama_kegiatan' => $baseName . ' Batch ' . ($i + 1),
                    'jenis_diklat_id' => $jenisDiklatId,
                    'kategori_diklat_id' => $faker->randomElement($kategoriDiklatIds),
                    'created_by' => $adminId,
                    'penyelenggara' => 'Kemenkes / BKN',
                    'tempat' => 'Hotel ' . $faker->city,
                    'tanggal_mulai' => $start->toDateString(),
                    'tanggal_selesai' => $end->toDateString(),
                    'waktu' => '08:00:00',
                    'jp' => $faker->randomElement([8, 16, 24, 32]),
                    'jenis_biaya_id' => $faker->randomElement($jenisBiayaIds),
                    'total_biaya' => $faker->numberBetween(1, 10) * 1000000,
                    'jenis_pelaksanaan' => $faker->randomElement(['internal', 'eksternal']),
                    'catatan' => 'Seeder Data ' . $start->format('Y-m'),
                ]);

                // Assign ke beberapa pegawai random (2 s.d 7 orang)
                $peserta = $pegawais->random($faker->numberBetween(2, 7));

                foreach ($peserta as $p) {
                    $statusDiklat = 'sudah terlaksana';
                    if ($statusWaktu === 2) {
                        $statusDiklat = 'belum terlaksana';
                    }

                    ListJadwalDiklat::create([
                        'diklat_id' => $diklat->id,
                        'pegawai_id' => $p->id,
                        'status_diklat' => $statusDiklat,
                        'status_kelayakan' => 'layak', // default layak agar masuk ke dashboard report
                        'sertif_file_path' => ($statusWaktu === 0) ? 'dokumen/dummy.pdf' : null,
                        'status_validasi' => ($statusWaktu === 0) ? 'valid' : null,
                    ]);
                }
            }
        }

        $this->command->info("Dashboard Diklat Seeder berhasil dijalankan! Data dummy untuk grafik Dashboard HRD/Direktur (ASN & Tenkes) sudah ditambahkan.");
    }
}
