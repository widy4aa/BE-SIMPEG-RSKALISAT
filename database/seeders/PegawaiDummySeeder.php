<?php

namespace Database\Seeders;

use App\Models\Anak;
use App\Models\GolonganRuang;
use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\JenisPegawai;
use App\Models\JenisSip;
use App\Models\KontakDarurat;
use App\Models\OrangTua;
use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\Pasangan;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\Pendidikan;
use App\Models\PenugasanKlinis;
use App\Models\Profesi;
use App\Models\Sip;
use App\Models\StrPegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PegawaiDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $unitKerjaIds = UnitKerja::pluck('id')->toArray();
        $profesiIds = Profesi::pluck('id')->toArray();
        $jenisPegawaiIds = JenisPegawai::pluck('id')->toArray();
        $golonganRuangIds = GolonganRuang::pluck('id')->toArray();
        $jenisSipIds = JenisSip::pluck('id')->toArray();

        // Check if master data is seeded
        if (empty($unitKerjaIds) || empty($profesiIds) || empty($jenisPegawaiIds) || empty($golonganRuangIds)) {
            $this->command->error("Master data (UnitKerja, Profesi, JenisPegawai, GolonganRuang) belum ada. Silakan jalankan HrisMasterDataSeeder terlebih dahulu.");
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            $nik = $faker->unique()->nik();
            $nip = $faker->unique()->numerify('19##########');
            $nama = $faker->name();

            // 1. Create User
            $user = User::create([
                'username' => $nik,
                'password' => Hash::make('password123'),
                'role' => 'pegawai',
                'is_active' => true,
            ]);

            // 2. Create Pangkat
            $pangkat = Pangkat::create([
                'nama' => $faker->randomElement(['Penata Muda', 'Pengatur', 'Pembina', 'Juru']),
                'pejabat_penetap' => $faker->company(),
                'tmt_sk' => $faker->date(),
                'sk_file_path' => null,
            ]);

            // 3. Create Jabatan
            $jabatan = Jabatan::create([
                'unit_kerja_id' => $faker->randomElement($unitKerjaIds),
                'nama' => $faker->jobTitle(),
                'tmt_mulai' => $faker->date(),
                'tmt_selesai' => null,
                'sk_file_path' => null,
            ]);

            // 4. Create Pegawai
            $pegawai = Pegawai::create([
                'user_id' => $user->id,
                'nip' => $nip,
                'nik' => $nik,
                'nama' => $nama,
                'jenis_pegawai_id' => $faker->randomElement($jenisPegawaiIds),
                'profesi_id' => $faker->randomElement($profesiIds),
                'jabatan_id' => $jabatan->id,
                'pangkat_id' => $pangkat->id,
                'golongan_ruang_id' => $faker->randomElement($golonganRuangIds),
                'status_pegawai' => $faker->randomElement(['aktif', 'tidak aktif']),
                'tgl_masuk' => $faker->date(),
                'tmt_cpns' => $faker->date(),
                'tmt_pns' => $faker->date(),
                'tmt_pangkat_akhir' => $faker->date(),
            ]);

            // Create Pivot Pangkat & Jabatan Pegawai
            PangkatPegawai::create([
                'pegawai_id' => $pegawai->id,
                'pangkat_id' => $pangkat->id,
            ]);
            JabatanPegawai::create([
                'pegawai_id' => $pegawai->id,
                'jabatan_id' => $jabatan->id,
            ]);

            // 5. Create Pegawai Pribadi
            $pribadi = PegawaiPribadi::create([
                'pegawai_id' => $pegawai->id,
                'tanggal_lahir' => $faker->date('Y-m-d', '-25 years'),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                'status_perkawinan' => $faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
                'pendidikan_terakhir' => $faker->randomElement(['SMA/SMK Sederajat', 'D3', 'S1/D4', 'S2', 'S3']),
                'alamat' => $faker->address(),
                'no_telp' => $faker->phoneNumber(),
                'email' => $faker->unique()->safeEmail(),
            ]);

            // 6. Create Pasangan
            if ($pribadi->status_perkawinan === 'Kawin') {
                Pasangan::create([
                    'pegawai_pribadi_id' => $pribadi->id,
                    'nama_lengkap' => $faker->name(),
                    'nik' => $faker->unique()->nik(),
                    'tempat_lahir' => $faker->city(),
                    'tanggal_lahir' => $faker->date('Y-m-d', '-25 years'),
                    'pekerjaan' => $faker->jobTitle(),
                    'status_pernikahan' => 'Sah',
                    'tanggal_pernikahan' => $faker->date(),
                    'status_tanggungan' => true,
                ]);
            }

            // 7. Create Anak (0 to 3 anak)
            $jumlahAnak = $faker->numberBetween(0, 3);
            for ($a = 0; $a < $jumlahAnak; $a++) {
                Anak::create([
                    'pegawai_pribadi_id' => $pribadi->id,
                    'nama_lengkap' => $faker->name(),
                    'nik' => $faker->unique()->nik(),
                    'tempat_lahir' => $faker->city(),
                    'tanggal_lahir' => $faker->date('Y-m-d', '-5 years'),
                    'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                    'status_anak' => 'Kandung',
                    'pendidikan_terakhir' => $faker->randomElement(['SD', 'SMP', 'Belum Sekolah']),
                    'status_tanggungan' => true,
                ]);
            }

            // 8. Create Orang Tua
            OrangTua::create([
                'pegawai_pribadi_id' => $pribadi->id,
                'nama_ayah' => $faker->name('male'),
                'nama_ibu' => $faker->name('female'),
                'status_hidup' => $faker->randomElement(['Hidup', 'Meninggal']),
                'alamat' => $faker->address(),
            ]);

            // 9. Create Kontak Darurat
            KontakDarurat::create([
                'pegawai_pribadi_id' => $pribadi->id,
                'nama_kontak' => $faker->name(),
                'hubungan_keluarga' => $faker->randomElement(['Saudara Kandung', 'Paman', 'Bibi']),
                'nomor_hp' => $faker->phoneNumber(),
                'alamat' => $faker->address(),
            ]);

            // 10. Create STR
            StrPegawai::create([
                'pegawai_id' => $pegawai->id,
                'nomor_str' => $faker->numerify('STR-###########'),
                'tanggal_terbit' => $faker->date(),
                'tanggal_kadaluarsa' => $faker->date('Y-m-d', '+5 years'),
            ]);

            // 11. Create SIP
            if (!empty($jenisSipIds)) {
                Sip::create([
                    'pegawai_id' => $pegawai->id,
                    'jenis_sip_id' => $faker->randomElement($jenisSipIds),
                    'nomor_sip' => $faker->numerify('SIP-###########'),
                    'tanggal_terbit' => $faker->date(),
                    'tanggal_kadaluarsa' => $faker->date('Y-m-d', '+5 years'),
                ]);
            }

            // 12. Create Penugasan Klinis
            PenugasanKlinis::create([
                'pegawai_id' => $pegawai->id,
                'nomor_surat' => $faker->numerify('SPK-###########'),
                'tgl_mulai' => $faker->date(),
                'tgl_kadaluarsa' => $faker->date('Y-m-d', '+5 years'),
            ]);

            // 13. Create Pendidikan
            Pendidikan::create([
                'pegawai_pribadi_id' => $pribadi->id,
                'jenjang' => $pribadi->pendidikan_terakhir,
                'institusi' => $faker->company(),
                'jurusan' => $faker->word(),
                'tahun_lulus' => $faker->year(),
                'nomor_ijazah' => $faker->numerify('IJZ-###########'),
            ]);
        }
        
        $this->command->info("Berhasil melakukan seeding 10 pegawai dengan data lengkap dan beragam!");
    }
}
