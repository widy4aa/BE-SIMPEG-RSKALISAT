<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\JenisPegawai;
use App\Models\JenisSip;
use App\Models\Pasangan;
use App\Models\Anak;
use App\Models\NotificationModel;
use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\PenugasanKlinis;
use App\Models\Profesi;
use App\Models\ProfesiPegawai;
use App\Models\Sip;
use App\Models\StrPegawai;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            ['tmt_mulai' => now()->toDateString(), 'unit_kerja_id' => $unitKerja->id]
        );

        $jabatanHrd = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf HRD'],
            ['tmt_mulai' => now()->toDateString(), 'unit_kerja_id' => $unitKerja->id]
        );

        $jabatanDirektur = Jabatan::query()->firstOrCreate(
            ['nama' => 'Direktur'],
            ['tmt_mulai' => now()->toDateString(), 'unit_kerja_id' => $unitDireksi->id]
        );

        $jabatan = Jabatan::query()->firstOrCreate(
            ['nama' => 'Staf Kepegawaian'],
            ['tmt_mulai' => now()->toDateString(), 'unit_kerja_id' => $unitKerja->id]
        );

        $jenisPegawai = JenisPegawai::query()->firstOrCreate(['nama' => 'PNS']);
        DB::table('golongan_ruang')->updateOrInsert(['nama' => 'III/a'], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $golonganRuang = DB::table('golongan_ruang')->where('nama', 'III/a')->first();

        $profesi = Profesi::query()->firstOrCreate(
            ['nama' => 'Analis SDM'],
            ['kategori_tenaga' => 'Non Kesehatan']
        );

        $jenisSip = JenisSip::query()->firstOrCreate(['nama' => 'SIP Praktik Rumah Sakit']);

        $pegawaiSeeds = [
            [
                'nik' => '3174010101010099',
                'nip' => '198501012008011001',
                'nama' => 'Admin SIMPEG',
                'role' => 'admin',
                'jenis_kelamin' => 'L',
                'jabatan_id' => $jabatanAdmin->id,
                'foto_path' => 'dokumen/foto/admin-simpeg.jpg',
                'ktp_file_path' => 'dokumen/ktp/admin-simpeg.pdf',
                'kk_file_path' => 'dokumen/kk/admin-simpeg.pdf',
                'buku_nikah_file_path' => 'dokumen/buku_nikah/admin-simpeg.pdf',
                'pasangan' => [
                    [
                        'nama_lengkap' => 'Dina Sari',
                        'tanggal_lahir' => '1987-05-20',
                        'pekerjaan' => 'Guru',
                    ],
                ],
                'anak' => [
                    [
                        'nama_lengkap' => 'Rafi Pratama',
                        'tanggal_lahir' => '2013-02-15',
                        'status_anak' => 'Kandung',
                    ],
                ],
                'str' => [
                    [
                        'nomor_str' => 'STR-3174010101010099-01',
                        'tanggal_terbit' => '2023-01-01',
                        'tanggal_kadaluarsa' => '2027-12-31',
                        'sk_file_path' => 'dokumen/str/admin-simpeg.pdf',
                    ],
                ],
                'sip' => [
                    [
                        'nomor_sip' => 'SIP-3174010101010099-01',
                        'tanggal_terbit' => '2024-01-01',
                        'tanggal_kadaluarsa' => '2027-12-31',
                        'sk_file_path' => 'dokumen/sip/admin-simpeg.pdf',
                    ],
                ],
                'penugasan_klinis' => [
                    [
                        'nomor_surat' => 'SK-KLINIS-ADM-001',
                        'tgl_mulai' => '2024-01-01',
                        'tgl_kadaluarsa' => '2026-12-31',
                        'dokumen_file_path' => 'dokumen/penugasan/admin-simpeg.pdf',
                    ],
                ],
                'notifications' => [
                    [
                        'title' => 'Selamat Datang',
                        'message' => 'Akun admin SIMPEG berhasil dibuat.',
                        'is_read' => false,
                    ],
                ],
            ],
            [
                'nik' => '3174010101010098',
                'nip' => '198601022009012002',
                'nama' => 'HRD SIMPEG',
                'role' => 'hrd',
                'jenis_kelamin' => 'P',
                'jabatan_id' => $jabatanHrd->id,
                'foto_path' => 'dokumen/foto/hrd-simpeg.jpg',
                'ktp_file_path' => 'dokumen/ktp/hrd-simpeg.pdf',
                'kk_file_path' => 'dokumen/kk/hrd-simpeg.pdf',
                'buku_nikah_file_path' => 'dokumen/buku_nikah/hrd-simpeg.pdf',
                'pasangan' => [
                    [
                        'nama_lengkap' => 'Andi Wijaya',
                        'tanggal_lahir' => '1984-03-10',
                        'pekerjaan' => 'Wiraswasta',
                    ],
                ],
                'anak' => [
                    [
                        'nama_lengkap' => 'Nadia Putri',
                        'tanggal_lahir' => '2012-09-17',
                        'status_anak' => 'Kandung',
                    ],
                ],
                'str' => [
                    [
                        'nomor_str' => 'STR-3174010101010098-01',
                        'tanggal_terbit' => '2022-07-01',
                        'tanggal_kadaluarsa' => '2027-06-30',
                        'sk_file_path' => 'dokumen/str/hrd-simpeg.pdf',
                    ],
                ],
                'sip' => [
                    [
                        'nomor_sip' => 'SIP-3174010101010098-01',
                        'tanggal_terbit' => '2023-01-01',
                        'tanggal_kadaluarsa' => '2027-12-31',
                        'sk_file_path' => 'dokumen/sip/hrd-simpeg.pdf',
                    ],
                ],
                'penugasan_klinis' => [
                    [
                        'nomor_surat' => 'SK-KLINIS-HRD-001',
                        'tgl_mulai' => '2023-02-01',
                        'tgl_kadaluarsa' => '2026-01-31',
                        'dokumen_file_path' => 'dokumen/penugasan/hrd-simpeg.pdf',
                    ],
                ],
                'notifications' => [
                    [
                        'title' => 'Reminder Data',
                        'message' => 'Silakan cek kelengkapan data pegawai.',
                        'is_read' => false,
                    ],
                ],
            ],
            [
                'nik' => '3174010101010001',
                'nip' => '198901012010011001',
                'nama' => 'Budi Santoso',
                'role' => 'pegawai',
                'jenis_kelamin' => 'L',
                'jabatan_id' => $jabatan->id,
                'foto_path' => 'dokumen/foto/budi-santoso.jpg',
                'ktp_file_path' => 'dokumen/ktp/budi-santoso.pdf',
                'kk_file_path' => 'dokumen/kk/budi-santoso.pdf',
                'buku_nikah_file_path' => 'dokumen/buku_nikah/budi-santoso.pdf',
                'pasangan' => [
                    [
                        'nama_lengkap' => 'Ani Lestari',
                        'tanggal_lahir' => '1991-04-11',
                        'pekerjaan' => 'Perawat',
                    ],
                ],
                'anak' => [
                    [
                        'nama_lengkap' => 'Fajar Santoso',
                        'tanggal_lahir' => '2016-06-03',
                        'status_anak' => 'Kandung',
                    ],
                ],
                'str' => [
                    [
                        'nomor_str' => 'STR-3174010101010001-01',
                        'tanggal_terbit' => '2023-05-01',
                        'tanggal_kadaluarsa' => '2028-04-30',
                        'sk_file_path' => 'dokumen/str/budi-santoso.pdf',
                    ],
                ],
                'sip' => [
                    [
                        'nomor_sip' => 'SIP-3174010101010001-01',
                        'tanggal_terbit' => '2024-01-15',
                        'tanggal_kadaluarsa' => '2028-01-14',
                        'sk_file_path' => 'dokumen/sip/budi-santoso.pdf',
                    ],
                ],
                'penugasan_klinis' => [
                    [
                        'nomor_surat' => 'SK-KLINIS-BUDI-001',
                        'tgl_mulai' => '2024-03-01',
                        'tgl_kadaluarsa' => '2026-02-28',
                        'dokumen_file_path' => 'dokumen/penugasan/budi-santoso.pdf',
                    ],
                ],
                'notifications' => [
                    [
                        'title' => 'Update Profil',
                        'message' => 'Profil Anda telah diperbarui.',
                        'is_read' => true,
                    ],
                ],
            ],
            [
                'nik' => '3174010101010003',
                'nip' => '198807072009011003',
                'nama' => 'Agus Priyanto',
                'role' => 'direktur',
                'jenis_kelamin' => 'L',
                'jabatan_id' => $jabatanDirektur->id,
                'foto_path' => 'dokumen/foto/agus-priyanto.jpg',
                'ktp_file_path' => 'dokumen/ktp/agus-priyanto.pdf',
                'kk_file_path' => 'dokumen/kk/agus-priyanto.pdf',
                'buku_nikah_file_path' => 'dokumen/buku_nikah/agus-priyanto.pdf',
                'pasangan' => [
                    [
                        'nama_lengkap' => 'Rina Anggraini',
                        'tanggal_lahir' => '1988-11-01',
                        'pekerjaan' => 'ASN',
                    ],
                ],
                'anak' => [],
                'str' => [
                    [
                        'nomor_str' => 'STR-3174010101010003-01',
                        'tanggal_terbit' => '2021-01-01',
                        'tanggal_kadaluarsa' => '2026-12-31',
                        'sk_file_path' => 'dokumen/str/agus-priyanto.pdf',
                    ],
                ],
                'sip' => [
                    [
                        'nomor_sip' => 'SIP-3174010101010003-01',
                        'tanggal_terbit' => '2021-02-01',
                        'tanggal_kadaluarsa' => '2026-01-31',
                        'sk_file_path' => 'dokumen/sip/agus-priyanto.pdf',
                    ],
                ],
                'penugasan_klinis' => [
                    [
                        'nomor_surat' => 'SK-KLINIS-DIR-001',
                        'tgl_mulai' => '2023-01-01',
                        'tgl_kadaluarsa' => '2026-12-31',
                        'dokumen_file_path' => 'dokumen/penugasan/agus-priyanto.pdf',
                    ],
                ],
                'notifications' => [
                    [
                        'title' => 'Laporan Bulanan',
                        'message' => 'Laporan bulanan SDM siap untuk ditinjau.',
                        'is_read' => false,
                    ],
                ],
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
                    'pangkat_id' => null, // will update below
                    'golongan_ruang_id' => $golonganRuang?->id,
                    'tmt_cpns' => '2020-01-01',
                    'tmt_pns' => '2021-01-01',
                    'tmt_pangkat_akhir' => '2022-01-01',
                ]
            );

            $newPangkat = \App\Models\Pangkat::query()->create([
                'nama' => 'Penata Muda',
                'pejabat_penetap' => 'Gubernur',
                'tmt_sk' => '2020-01-01'
            ]);

            $pegawai->update(['pangkat_id' => $newPangkat->id]);

            $pegawaiPribadi = PegawaiPribadi::query()->updateOrCreate(
                ['pegawai_id' => $pegawai->id],
                [
                    'pendidikan_terakhir' => 'S1/D4',
                    'tanggal_lahir' => '1990-01-01',
                    'jenis_kelamin' => $seed['jenis_kelamin'],
                    'agama' => 'Islam',
                    'status_perkawinan' => 'kawin',
                    'alamat' => 'Jakarta',
                    'no_telp' => '081234567890',
                    'email' => strtolower(str_replace(' ', '.', $seed['nama'])).'@example.com',
                    'foto_path' => $seed['foto_path'],
                    'ktp_file_path' => $seed['ktp_file_path'],
                    'kk_file_path' => $seed['kk_file_path'],
                    'buku_nikah_file_path' => $seed['buku_nikah_file_path'],
                ]
            );

            Pasangan::query()->where('pegawai_pribadi_id', $pegawaiPribadi->id)->delete();
            foreach ($seed['pasangan'] as $p) {
                Pasangan::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'nama_lengkap' => $p['nama_lengkap'],
                    'tanggal_lahir' => $p['tanggal_lahir'],
                    'pekerjaan' => $p['pekerjaan'] ?? null,
                ]);
            }

            Anak::query()->where('pegawai_pribadi_id', $pegawaiPribadi->id)->delete();
            foreach ($seed['anak'] as $a) {
                Anak::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'nama_lengkap' => $a['nama_lengkap'],
                    'tanggal_lahir' => $a['tanggal_lahir'],
                    'status_anak' => $a['status_anak'] ?? null,
                ]);
            }

            StrPegawai::query()->where('pegawai_id', $pegawai->id)->delete();
            foreach ($seed['str'] as $str) {
                StrPegawai::query()->create([
                    'pegawai_id' => $pegawai->id,
                    'nomor_str' => $str['nomor_str'],
                    'tanggal_terbit' => $str['tanggal_terbit'],
                    'tanggal_kadaluarsa' => $str['tanggal_kadaluarsa'],
                    'sk_file_path' => $str['sk_file_path'],
                ]);
            }

            Sip::query()->where('pegawai_id', $pegawai->id)->delete();
            foreach ($seed['sip'] as $sip) {
                Sip::query()->create([
                    'pegawai_id' => $pegawai->id,
                    'jenis_sip_id' => $jenisSip->id,
                    'nomor_sip' => $sip['nomor_sip'],
                    'tanggal_terbit' => $sip['tanggal_terbit'],
                    'tanggal_kadaluarsa' => $sip['tanggal_kadaluarsa'],
                    'sk_file_path' => $sip['sk_file_path'],
                ]);
            }

            PenugasanKlinis::query()->where('pegawai_id', $pegawai->id)->delete();
            foreach ($seed['penugasan_klinis'] as $penugasan) {
                PenugasanKlinis::query()->create([
                    'pegawai_id' => $pegawai->id,
                    'nomor_surat' => $penugasan['nomor_surat'],
                    'tgl_mulai' => $penugasan['tgl_mulai'],
                    'tgl_kadaluarsa' => $penugasan['tgl_kadaluarsa'],
                    'dokumen_file_path' => $penugasan['dokumen_file_path'],
                ]);
            }

            NotificationModel::query()->where('user_id', $user->id)->delete();
            foreach ($seed['notifications'] as $notification) {
                NotificationModel::query()->create([
                    'user_id' => $user->id,
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'is_read' => $notification['is_read'],
                ]);
            }

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
                    'pangkat_id' => $newPangkat->id,
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
        }
    }
}
