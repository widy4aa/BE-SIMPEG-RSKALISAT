<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\JenisPegawai;
use App\Models\Pegawai;
use App\Models\PegawaiPekerjaan;
use App\Models\PegawaiPribadi;
use App\Models\Profesi;
use App\Models\RiwayatPekerjaan;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $unitKerja = UnitKerja::query()->firstOrCreate(['nama' => 'SDM']);
        $unitDireksi = UnitKerja::query()->firstOrCreate(['nama' => 'Direksi']);

        $jabatanAdmin = Jabatan::query()->firstOrCreate(
            ['nama' => 'Administrator Sistem', 'unit_kerja_id' => $unitKerja->id],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jabatanHrd = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf HRD', 'unit_kerja_id' => $unitKerja->id],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jabatanDirektur = Jabatan::query()->firstOrCreate(
            ['nama' => 'Direktur', 'unit_kerja_id' => $unitDireksi->id],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf Kepegawaian', 'unit_kerja_id' => $unitKerja->id],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jenisPegawai = JenisPegawai::query()->firstOrCreate(['nama' => 'PNS']);
        $profesi = Profesi::query()->firstOrCreate(
            ['nama' => 'Analis SDM'],
            ['kategori_tenaga' => 'Non Kesehatan']
        );

        $pegawaiSeeds = [
            [
                'nik' => '3174010101010099',
                'nip' => '198501012008011001',
                'nama' => 'Admin SIMPEG',
                'role' => 'admin',
                'jenis_kelamin' => 'L',
                'jabatan_id' => $jabatanAdmin->id,
            ],
            [
                'nik' => '3174010101010098',
                'nip' => '198601022009012002',
                'nama' => 'HRD SIMPEG',
                'role' => 'hrd',
                'jenis_kelamin' => 'P',
                'jabatan_id' => $jabatanHrd->id,
            ],
            [
                'nik' => '3174010101010001',
                'nip' => '198901012010011001',
                'nama' => 'Budi Santoso',
                'role' => 'pegawai',
                'jenis_kelamin' => 'L',
                'jabatan_id' => $jabatan->id,
            ],
            [
                'nik' => '3174010101010002',
                'nip' => '199202022012012002',
                'nama' => 'Siti Rahma',
                'role' => 'pegawai',
                'jenis_kelamin' => 'P',
                'jabatan_id' => $jabatan->id,
            ],
            [
                'nik' => '3174010101010003',
                'nip' => '198807072009011003',
                'nama' => 'Agus Priyanto',
                'role' => 'direktur',
                'jenis_kelamin' => 'L',
                'jabatan_id' => $jabatanDirektur->id,
            ],
        ];

        foreach ($pegawaiSeeds as $seed) {
            $user = User::query()->updateOrCreate(
                ['username' => $seed['nik']],
                [
                    'password' => Hash::make('password'),
                    'role' => $seed['role'],
                    'is_active' => true,
                ]
            );

            $pegawai = Pegawai::query()->updateOrCreate(
                ['nik' => $seed['nik']],
                [
                    'user_id' => $user->id,
                    'nip' => $seed['nip'],
                    'nama' => $seed['nama'],
                ]
            );

            PegawaiPribadi::query()->updateOrCreate(
                ['pegawai_id' => $pegawai->id],
                [
                    'pendidikan_terakhir' => 'S1/D4',
                    'tanggal_lahir' => '1990-01-01',
                    'jenis_kelamin' => $seed['jenis_kelamin'],
                    'agama' => 'Islam',
                    'status_perkawinan' => 'kawin',
                    'alamat' => 'Jakarta',
                    'no_telp' => '081234567890',
                    'email' => strtolower(str_replace(' ', '.', $seed['nama'])) . '@example.com',
                ]
            );

            $pekerjaan = PegawaiPekerjaan::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'jabatan_id' => $seed['jabatan_id'],
                ],
                [
                    'jenis_pegawai_id' => $jenisPegawai->id,
                    'profesi_id' => $profesi->id,
                    'status_pegawai' => 'aktif',
                    'tgl_masuk' => '2020-01-01',
                    'golongan_ruang' => 'III/a',
                ]
            );

            RiwayatPekerjaan::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'pegawai_pekerjaan_id' => $pekerjaan->id,
                    'is_current' => true,
                ],
                [
                    'jabatan_id' => $seed['jabatan_id'],
                    'started_at' => '2020-01-01',
                    'note' => 'Data awal dari seeder',
                ]
            );
        }
    }
}
