<?php

namespace Database\Seeders;

use App\Models\Anak;
use App\Models\Diklat;
use App\Models\GolonganRuang;
use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\JenisPegawai;
use App\Models\KontakDarurat;
use App\Models\ListJadwalDiklat;
use App\Models\OrangTua;
use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\Pasangan;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\Pendidikan;
use App\Models\Profesi;
use App\Models\UnitKerja;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiMassSeeder extends Seeder
{
    private const TOTAL_USERS = 500;

    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ensure at least one master data exists
        $unitKerja = UnitKerja::query()->firstOrCreate(['nama' => 'Instalasi Rawat Inap']);
        $jabatanDefault = Jabatan::query()->firstOrCreate(
            ['nama' => 'Perawat Pelaksana'],
            ['tmt_mulai' => now()->toDateString(), 'unit_kerja_id' => $unitKerja->id]
        );
        $jenisPegawaiDefault = JenisPegawai::query()->firstOrCreate(['nama' => 'PNS']);
        $profesiDefault = Profesi::query()->firstOrCreate(
            ['nama' => 'Perawat'],
            ['kategori_tenaga' => 'Kesehatan']
        );
        DB::table('golongan_ruang')->updateOrInsert(['nama' => 'II/c'], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $golonganRuangDefault = DB::table('golongan_ruang')->where('nama', 'II/c')->first();
        $pangkatDefault = Pangkat::query()->firstOrCreate([
            'nama' => 'Pengatur',
            'pejabat_penetap' => 'Bupati',
            'tmt_sk' => '2015-01-01'
        ]);
        $diklatDefault = Diklat::query()->firstOrCreate([
            'nama_kegiatan' => 'Pelatihan Basic Life Support (BLS)',
            'penyelenggara' => 'RSUD Dr. Soebandi Jember',
            'tanggal_mulai' => '2022-03-10',
            'tanggal_selesai' => '2022-03-12',
            'jp' => 24,
        ]);

        $jabatanIds = Jabatan::pluck('id')->toArray();
        $jenisPegawaiIds = JenisPegawai::pluck('id')->toArray();
        $profesiIds = Profesi::pluck('id')->toArray();
        $pangkatIds = Pangkat::pluck('id')->toArray();
        $golonganIds = DB::table('golongan_ruang')->pluck('id')->toArray();
        $diklatIds = Diklat::pluck('id')->toArray();

        $defaultPassword = Hash::make('password');
        $dummyFilePath = 'dokumen/dummy.pdf';
        $dummyPhotoPath = 'dokumen/dummy_photo.jpg';

        $this->command->getOutput()->progressStart(self::TOTAL_USERS);

        for ($i = 1; $i <= self::TOTAL_USERS; $i++) {
            DB::transaction(function () use ($faker, $jabatanIds, $jenisPegawaiIds, $profesiIds, $pangkatIds, $golonganIds, $diklatIds, $defaultPassword, $dummyFilePath, $dummyPhotoPath) {
                
                $nik = $faker->unique()->nik();
                $nip = $faker->unique()->numerify('198########20##011###');
                $nama = $faker->name();

                $user = User::query()->create([
                    'username' => $nik,
                    'password' => $defaultPassword,
                    'role' => 'pegawai',
                    'is_active' => true,
                ]);

                $jabatanId = $faker->randomElement($jabatanIds);
                $pangkatId = $faker->randomElement($pangkatIds);

                $pegawai = Pegawai::query()->create([
                    'user_id' => $user->id,
                    'nik' => $nik,
                    'nip' => $nip,
                    'nama' => $nama,
                    'jenis_pegawai_id' => $faker->randomElement($jenisPegawaiIds),
                    'profesi_id' => $faker->randomElement($profesiIds),
                    'jabatan_id' => $jabatanId,
                    'status_pegawai' => 'aktif',
                    'tgl_masuk' => $faker->dateTimeBetween('-15 years', '-1 years')->format('Y-m-d'),
                    'pangkat_id' => $pangkatId,
                    'golongan_ruang_id' => $faker->randomElement($golonganIds),
                    'tmt_cpns' => $faker->dateTimeBetween('-15 years', '-10 years')->format('Y-m-d'),
                    'tmt_pns' => $faker->dateTimeBetween('-10 years', '-5 years')->format('Y-m-d'),
                    'tmt_pangkat_akhir' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
                ]);

                $pegawaiPribadi = PegawaiPribadi::query()->create([
                    'pegawai_id' => $pegawai->id,
                    'pendidikan_terakhir' => $faker->randomElement(['SMA/SMK Sederajat', 'D3', 'S1/D4', 'S2', 'S3']),
                    'tanggal_lahir' => $faker->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
                    'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                    'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                    'status_perkawinan' => $faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
                    'alamat' => $faker->address(),
                    'no_telp' => $faker->phoneNumber(),
                    'email' => $faker->unique()->safeEmail(),
                    'foto_path' => $dummyPhotoPath,
                    'ktp_file_path' => $dummyFilePath,
                    'kk_file_path' => $dummyFilePath,
                    'buku_nikah_file_path' => $dummyFilePath,
                ]);

                // Riwayat JabatanPegawai
                JabatanPegawai::query()->create([
                    'pegawai_id' => $pegawai->id,
                    'jabatan_id' => $jabatanId,
                    'is_current' => true,
                    'started_at' => $faker->dateTimeBetween('-3 years', 'now')->format('Y-m-d'),
                    'note' => 'Generated by seeder'
                ]);

                // Riwayat PangkatPegawai
                PangkatPegawai::query()->create([
                    'pegawai_id' => $pegawai->id,
                    'pangkat_id' => $pangkatId,
                    'is_current' => true,
                    'started_at' => $pegawai->tmt_pangkat_akhir,
                    'note' => 'Generated by seeder'
                ]);

                // Pendidikan
                Pendidikan::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'jenjang' => $pegawaiPribadi->pendidikan_terakhir,
                    'institusi' => $faker->company(),
                    'jurusan' => $faker->jobTitle(),
                    'tahun_lulus' => $faker->numberBetween(1995, 2020),
                    'nomor_ijazah' => $faker->numerify('IJZ-####/##/####'),
                    'ijazah_file_path' => $dummyFilePath,
                ]);

                // Pasangan (If Kawin)
                if ($pegawaiPribadi->status_perkawinan === 'Kawin') {
                    Pasangan::query()->create([
                        'pegawai_pribadi_id' => $pegawaiPribadi->id,
                        'nama_lengkap' => $faker->name(),
                        'nik' => $faker->unique()->nik(),
                        'tempat_lahir' => $faker->city(),
                        'tanggal_lahir' => $faker->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
                        'pekerjaan' => $faker->jobTitle(),
                        'instansi' => $faker->company(),
                        'status_pernikahan' => 'Kawin',
                        'tanggal_pernikahan' => $faker->dateTimeBetween('-10 years', '-1 years')->format('Y-m-d'),
                        'nomor_buku_nikah' => $faker->numerify('BKN-####-####'),
                        'status_tanggungan' => $faker->boolean(),
                        'npwp_pasangan' => $faker->numerify('##.###.###.#-###.###'),
                        'buku_nikah_file_path' => $dummyFilePath,
                    ]);

                    // Anak
                    $numAnak = $faker->numberBetween(0, 3);
                    for ($a = 0; $a < $numAnak; $a++) {
                        Anak::query()->create([
                            'pegawai_pribadi_id' => $pegawaiPribadi->id,
                            'nama_lengkap' => $faker->name(),
                            'nik' => $faker->unique()->nik(),
                            'tempat_lahir' => $faker->city(),
                            'tanggal_lahir' => $faker->dateTimeBetween('-20 years', 'now')->format('Y-m-d'),
                            'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                            'status_anak' => 'Anak Kandung',
                            'pendidikan_terakhir' => $faker->randomElement(['SD', 'SMP', 'SMA']),
                            'status_tanggungan' => $faker->boolean(),
                            'usia' => $faker->numberBetween(1, 20),
                            'keterangan_disabilitas' => '-',
                            'akta_kelahiran_file_path' => $dummyFilePath,
                        ]);
                    }
                }

                // OrangTua
                OrangTua::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'nama_ayah' => $faker->name('male'),
                    'nama_ibu' => $faker->name('female'),
                    'alamat' => $faker->address(),
                ]);

                // KontakDarurat
                KontakDarurat::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'nama_kontak' => $faker->name(),
                    'hubungan_keluarga' => $faker->randomElement(['Saudara Kandung', 'Paman', 'Bibi', 'Sepupu']),
                    'nomor_hp' => $faker->phoneNumber(),
                    'alamat' => $faker->address(),
                ]);

                // Diklat
                if (count($diklatIds) > 0 && $faker->boolean(70)) {
                    ListJadwalDiklat::query()->create([
                        'pegawai_id' => $pegawai->id,
                        'diklat_id' => $faker->randomElement($diklatIds),
                        'sertif_file_path' => $dummyFilePath,
                        'no_sertif' => $faker->numerify('SRT-####-####'),
                        'uploaded_at' => now(),
                        'status_diklat' => 'sudah terlaksana',
                    ]);
                }
            });

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Berhasil meng-generate ' . self::TOTAL_USERS . ' data pegawai beserta riwayatnya.');
    }
}
