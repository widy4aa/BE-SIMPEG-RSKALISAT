<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\JenisPegawai;
use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\ProfesiPegawai;
use App\Models\Profesi;
use App\Models\UnitKerjaPegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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
            ['nama' => 'Administrator Sistem'],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jabatanHrd = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf HRD'],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jabatanDirektur = Jabatan::query()->firstOrCreate(
            ['nama' => 'Direktur'],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf Kepegawaian'],
            ['tmt_mulai' => now()->toDateString()]
        );

        $jenisPegawai = JenisPegawai::query()->firstOrCreate(['nama' => 'PNS']);
        $pangkat = Pangkat::query()->firstOrCreate(['nama' => 'Penata Muda']);
        DB::table('golongan_ruang')->updateOrInsert(['nama' => 'III/a'], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $golonganRuang = DB::table('golongan_ruang')->where('nama', 'III/a')->first();

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
                    'jenis_pegawai_id' => $jenisPegawai->id,
                    'profesi_id' => $profesi->id,
                    'jabatan_id' => $seed['jabatan_id'],
                    'status_pegawai' => 'aktif',
                    'tgl_masuk' => '2020-01-01',
                    'pangkat_id' => $pangkat->id,
                    'golongan_ruang_id' => $golonganRuang?->id,
                    'tmt_cpns' => '2020-01-01',
                    'tmt_pns' => '2021-01-01',
                    'tmt_pangkat_akhir' => '2022-01-01',
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

            ProfesiPegawai::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'is_current' => true,
                ],
                [
                    'profesi_id' => $profesi->id,
                    'started_at' => '2020-01-01',
                    'note' => 'Data awal dari seeder',
                ]
            );

            PangkatPegawai::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'is_current' => true,
                ],
                [
                    'pangkat_id' => $pangkat->id,
                    'started_at' => '2020-01-01',
                    'note' => 'Data awal dari seeder',
                ]
            );

            JabatanPegawai::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'jabatan_id' => $seed['jabatan_id'],
                    'is_current' => true,
                ],
                [
                    'started_at' => '2020-01-01',
                    'note' => 'Data awal dari seeder',
                ]
            );

            UnitKerjaPegawai::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'unit_kerja_id' => $seed['role'] === 'direktur' ? $unitDireksi->id : $unitKerja->id,
                    'is_current' => true,
                ],
                [
                    'started_at' => '2020-01-01',
                    'note' => 'Data awal dari seeder',
                ]
            );
        }
    }
}
