<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\JenisPegawai;
use App\Models\Pangkat;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\Profesi;
use App\Models\UnitKerja;
use App\Models\UnitKerjaPegawai;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiLoadTestSeeder extends Seeder
{
    private const TOTAL_TEST_USERS = 100;

    /**
     * Seed 100 user pegawai untuk kebutuhan load/functional testing.
     */
    public function run(): void
    {
        $unitKerja = UnitKerja::query()->firstOrCreate(['nama' => 'SDM']);
        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf Kepegawaian'],
            ['tmt_mulai' => now()->toDateString()]
        );
        $jenisPegawai = JenisPegawai::query()->firstOrCreate(['nama' => 'PNS']);
        $profesi = Profesi::query()->firstOrCreate(
            ['nama' => 'Analis SDM'],
            ['kategori_tenaga' => 'Non Kesehatan']
        );
        $pangkat = Pangkat::query()->firstOrCreate(['nama' => 'Penata Muda']);

        DB::table('golongan_ruang')->updateOrInsert(['nama' => 'III/a'], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $golonganRuang = DB::table('golongan_ruang')->where('nama', 'III/a')->first();

        for ($i = 1; $i <= self::TOTAL_TEST_USERS; $i++) {
            $nik = sprintf('899900000000%04d', $i);
            $nip = sprintf('19990101202301%04d', $i);
            $nama = sprintf('Pegawai Test %03d', $i);

            $user = User::query()->updateOrCreate(
                ['username' => $nik],
                [
                    'password' => Hash::make('password'),
                    'role' => 'pegawai',
                    'is_active' => true,
                ]
            );

            $pegawai = Pegawai::query()->updateOrCreate(
                ['nik' => $nik],
                [
                    'user_id' => $user->id,
                    'nip' => $nip,
                    'nama' => $nama,
                    'jenis_pegawai_id' => $jenisPegawai->id,
                    'profesi_id' => $profesi->id,
                    'jabatan_id' => $jabatan->id,
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
                    'jenis_kelamin' => 'L',
                    'agama' => 'Islam',
                    'status_perkawinan' => 'kawin',
                    'alamat' => 'Alamat Testing '.$i,
                    'no_telp' => '081234567'.str_pad((string) ($i % 1000), 3, '0', STR_PAD_LEFT),
                    'email' => 'pegawai.test.'.$i.'@example.com',
                ]
            );

            UnitKerjaPegawai::query()->updateOrCreate(
                [
                    'pegawai_id' => $pegawai->id,
                    'unit_kerja_id' => $unitKerja->id,
                    'is_current' => true,
                ],
                [
                    'started_at' => '2020-01-01',
                    'note' => 'Data load test',
                ]
            );
        }
    }
}
