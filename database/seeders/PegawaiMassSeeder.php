<?php

namespace Database\Seeders;

use App\Models\Anak;
use App\Models\Diklat;
use App\Models\GolonganRuang;
use App\Models\Jabatan;
use App\Models\JabatanPegawai;
use App\Models\JenisDiklat;
use App\Models\JenisPegawai;
use App\Models\KategoriDiklat;
use App\Models\KontakDarurat;
use App\Models\ListJadwalDiklat;
use App\Models\NotificationModel;
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
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PegawaiMassSeeder extends Seeder
{
    private const TOTAL_USERS = 300;

    private const JABATAN_RS = [
        'Perawat Pelaksana',
        'Bidan Pelaksana',
        'Dokter Umum',
        'Dokter Spesialis Penyakit Dalam',
        'Dokter Spesialis Bedah',
        'Apoteker',
        'Analis Kesehatan',
        'Radiografer',
        'Fisioterapis',
        'Nutrisionis',
        'Rekam Medis',
        'Sanitarian',
        'Pengelola Keuangan',
        'Pengelola Kepegawaian',
        'Pranata Komputer',
        'Pengadministrasi Umum',
        'Kepala Ruangan Rawat Inap',
        'Kepala Ruangan IGD',
        'Kepala Instalasi Farmasi',
        'Kepala Seksi Keperawatan',
    ];

    private const PANGKAT_GOLONGAN = [
        ['pangkat' => 'Juru Muda',             'golongan' => 'I/a'],
        ['pangkat' => 'Juru Muda Tingkat I',   'golongan' => 'I/b'],
        ['pangkat' => 'Juru',                  'golongan' => 'I/c'],
        ['pangkat' => 'Juru Tingkat I',        'golongan' => 'I/d'],
        ['pangkat' => 'Pengatur Muda',         'golongan' => 'II/a'],
        ['pangkat' => 'Pengatur Muda Tk. I',   'golongan' => 'II/b'],
        ['pangkat' => 'Pengatur',              'golongan' => 'II/c'],
        ['pangkat' => 'Pengatur Tingkat I',    'golongan' => 'II/d'],
        ['pangkat' => 'Penata Muda',           'golongan' => 'III/a'],
        ['pangkat' => 'Penata Muda Tingkat I', 'golongan' => 'III/b'],
        ['pangkat' => 'Penata',                'golongan' => 'III/c'],
        ['pangkat' => 'Penata Tingkat I',      'golongan' => 'III/d'],
        ['pangkat' => 'Pembina',               'golongan' => 'IV/a'],
        ['pangkat' => 'Pembina Tingkat I',     'golongan' => 'IV/b'],
    ];

    private const DIKLAT_RS = [
        ['nama' => 'Pelatihan Basic Life Support (BLS)',                    'jp' => 24,  'jenis' => 'Tenkes',  'kategori' => 'Teknis'],
        ['nama' => 'Pelatihan Pencegahan dan Pengendalian Infeksi (PPI)',   'jp' => 20,  'jenis' => 'Tenkes',  'kategori' => 'Teknis'],
        ['nama' => 'Pelatihan Keselamatan Pasien (Patient Safety)',         'jp' => 16,  'jenis' => 'Tenkes',  'kategori' => 'Teknis'],
        ['nama' => 'Pelatihan Manajemen Nyeri',                            'jp' => 30,  'jenis' => 'Tenkes',  'kategori' => 'Fungsional'],
        ['nama' => 'Pelatihan Triage IGD',                                 'jp' => 40,  'jenis' => 'Tenkes',  'kategori' => 'Teknis'],
        ['nama' => 'Pelatihan Wound Care Management',                      'jp' => 36,  'jenis' => 'Tenkes',  'kategori' => 'Fungsional'],
        ['nama' => 'Pelatihan K3RS (Keselamatan Kerja Rumah Sakit)',        'jp' => 20,  'jenis' => 'ASN',     'kategori' => 'Teknis'],
        ['nama' => 'Pelatihan Pelayanan Prima',                            'jp' => 16,  'jenis' => 'ASN',     'kategori' => 'Struktural'],
        ['nama' => 'Diklat Prajabatan CPNS',                               'jp' => 120, 'jenis' => 'ASN',     'kategori' => 'Struktural'],
        ['nama' => 'Pelatihan Rekam Medis Berbasis Elektronik',            'jp' => 24,  'jenis' => 'ASN',     'kategori' => 'Teknis'],
        ['nama' => 'Pelatihan Komunikasi Terapeutik',                      'jp' => 20,  'jenis' => 'Tenkes',  'kategori' => 'Fungsional'],
        ['nama' => 'Pelatihan Akreditasi Rumah Sakit (SNARS)',              'jp' => 32,  'jenis' => 'ASN',     'kategori' => 'Akred'],
        ['nama' => 'Pelatihan Pengadaan Barang dan Jasa Pemerintah',       'jp' => 120, 'jenis' => 'ASN',     'kategori' => 'Struktural'],
        ['nama' => 'Pelatihan Kepemimpinan Administrator (PKA)',            'jp' => 400, 'jenis' => 'ASN',     'kategori' => 'Struktural'],
    ];

    private const NOTIF_TEMPLATES = [
        'info' => [
            ['title' => 'Pengingat Kelengkapan Profil', 'message' => 'Mohon segera lengkapi data profil Anda agar informasi kepegawaian lebih lengkap.'],
            ['title' => 'Jadwal Diklat Baru', 'message' => 'Anda terdaftar dalam jadwal diklat baru. Silakan cek detail pada menu Diklat.'],
            ['title' => 'Perubahan Data Disetujui', 'message' => 'Permintaan perubahan data Anda telah disetujui oleh admin kepegawaian.'],
            ['title' => 'Data Karir Diperbarui', 'message' => 'Riwayat jabatan Anda telah diperbarui sesuai SK terbaru. Silakan verifikasi.'],
            ['title' => 'Pengumuman Sistem', 'message' => 'Sistem SIMPEG akan menjalani pemeliharaan pada hari Minggu pukul 23:00 - 01:00 WIB.'],
            ['title' => 'Evaluasi Kinerja', 'message' => 'Periode evaluasi kinerja semester ini telah dibuka. Harap lengkapi formulir evaluasi Anda.'],
        ],
        'warning' => [
            ['title' => 'STR Akan Segera Kadaluarsa', 'message' => 'STR Anda akan kadaluarsa dalam 30 hari ke depan. Segera lakukan perpanjangan.'],
            ['title' => 'SIP Mendekati Masa Berakhir', 'message' => 'SIP Anda akan berakhir dalam 60 hari. Pastikan perpanjangan telah diproses.'],
            ['title' => 'Data Tidak Lengkap', 'message' => 'Terdapat data wajib yang belum diisi. Lengkapi segera untuk menghindari kendala administrasi.'],
            ['title' => 'Masa Kontrak Mendekati Akhir', 'message' => 'Kontrak kerja Anda akan berakhir dalam 90 hari. Hubungi bagian SDM untuk informasi perpanjangan.'],
        ],
        'success' => [
            ['title' => 'Sertifikat Diklat Terverifikasi', 'message' => 'Sertifikat diklat Anda telah berhasil diverifikasi oleh admin. Data diklat sudah tercatat.'],
            ['title' => 'Profil Berhasil Diperbarui', 'message' => 'Data profil Anda telah berhasil diperbarui dan disimpan dalam sistem.'],
            ['title' => 'Kenaikan Pangkat Dikonfirmasi', 'message' => 'Kenaikan pangkat Anda telah dikonfirmasi. Data kepegawaian telah diperbarui secara otomatis.'],
            ['title' => 'Diklat Selesai Dilaksanakan', 'message' => 'Anda telah berhasil menyelesaikan diklat. Sertifikat dapat diunduh melalui menu Diklat.'],
            ['title' => 'Akun Berhasil Diaktifkan', 'message' => 'Akun SIMPEG Anda telah berhasil diaktifkan. Selamat menggunakan sistem kepegawaian.'],
        ],
    ];

    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $this->command->info('Mempersiapkan master data...');
        $this->seedMasterData($faker);

        $jabatanIds      = Jabatan::pluck('id')->toArray();
        $jenisPegawaiIds = JenisPegawai::pluck('id')->toArray();
        $profesiIds      = Profesi::pluck('id')->toArray();
        $diklatIds       = Diklat::pluck('id')->toArray();

        // Build ordered golongan+pangkat pairs for realistic career progression
        $careerLadder = $this->buildCareerLadder();

        $defaultPassword  = Hash::make('password');
        $dummyFilePath    = 'dokumen/dummy.pdf';
        $dummyPhotoPath   = 'dokumen/dummy_photo.jpg';
        $appStartDate     = Carbon::now()->subYear(); // simulate 1 year of operation

        $this->command->getOutput()->progressStart(self::TOTAL_USERS);

        for ($i = 1; $i <= self::TOTAL_USERS; $i++) {
            DB::transaction(function () use (
                $faker, $jabatanIds, $jenisPegawaiIds, $profesiIds, $diklatIds,
                $careerLadder, $defaultPassword, $dummyFilePath, $dummyPhotoPath, $appStartDate
            ) {
                $nik  = $faker->unique()->numerify('3###############');
                $nip  = $faker->unique()->numerify('19########200###1###');
                $nama = $faker->name();

                // --- User ---
                $user = User::query()->create([
                    'username'  => $nik,
                    'password'  => $defaultPassword,
                    'role'      => 'pegawai',
                    'is_active' => true,
                ]);

                // --- Career ladder for this employee ---
                $ladderIndex  = $faker->numberBetween(0, count($careerLadder) - 1);
                $currentEntry = $careerLadder[$ladderIndex];
                $tglMasuk     = $faker->dateTimeBetween('-15 years', '-3 years');
                $tmtCpns      = Carbon::instance($tglMasuk)->addMonths($faker->numberBetween(0, 3))->toDateString();
                $tmtPns       = Carbon::parse($tmtCpns)->addYear()->toDateString();
                $tmtPangkat   = $faker->dateTimeBetween('-4 years', '-6 months')->format('Y-m-d');

                // --- Pegawai ---
                $jabatanId = $faker->randomElement($jabatanIds);
                $pegawai   = Pegawai::query()->create([
                    'user_id'          => $user->id,
                    'nik'              => $nik,
                    'nip'              => $nip,
                    'nama'             => $nama,
                    'jenis_pegawai_id' => $faker->randomElement($jenisPegawaiIds),
                    'profesi_id'       => $faker->randomElement($profesiIds),
                    'jabatan_id'       => $jabatanId,
                    'status_pegawai'   => 'aktif',
                    'tgl_masuk'        => $tglMasuk->format('Y-m-d'),
                    'pangkat_id'       => $currentEntry['pangkat_id'],
                    'golongan_ruang_id'=> $currentEntry['golongan_id'],
                    'tmt_cpns'         => $tmtCpns,
                    'tmt_pns'          => $tmtPns,
                    'tmt_pangkat_akhir'=> $tmtPangkat,
                ]);

                // --- Data Pribadi ---
                $statusPerkawinan = $faker->randomElement(['Belum Kawin', 'Belum Kawin', 'Kawin', 'Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
                $pegawaiPribadi   = PegawaiPribadi::query()->create([
                    'pegawai_id'         => $pegawai->id,
                    'pendidikan_terakhir'=> $faker->randomElement(['SMA/SMK Sederajat', 'D3', 'D3', 'S1/D4', 'S1/D4', 'S2']),
                    'tanggal_lahir'      => $faker->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
                    'jenis_kelamin'      => $faker->randomElement(['L', 'P']),
                    'agama'              => $faker->randomElement(['Islam', 'Islam', 'Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                    'status_perkawinan'  => $statusPerkawinan,
                    'alamat'             => $faker->address(),
                    'no_telp'            => '08' . $faker->numerify('##########'),
                    'email'              => $faker->unique()->safeEmail(),
                    'foto_path'          => $dummyPhotoPath,
                    'ktp_file_path'      => $dummyFilePath,
                    'kk_file_path'       => $dummyFilePath,
                    'buku_nikah_file_path' => $dummyFilePath,
                ]);

                // --- Pendidikan ---
                Pendidikan::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'jenjang'            => $pegawaiPribadi->pendidikan_terakhir,
                    'institusi'          => $faker->randomElement([
                        'Universitas Airlangga', 'Universitas Brawijaya', 'Universitas Jember',
                        'Politeknik Kesehatan Malang', 'STIKES Husada Jombang', 'Universitas Gadjah Mada',
                        'Universitas Indonesia', 'STIKES Banyuwangi',
                    ]),
                    'jurusan'            => $faker->randomElement([
                        'Keperawatan', 'Kebidanan', 'Kedokteran', 'Farmasi',
                        'Analis Kesehatan', 'Manajemen Kesehatan', 'Administrasi Rumah Sakit',
                    ]),
                    'tahun_lulus'        => $faker->numberBetween(1995, 2022),
                    'nomor_ijazah'       => $faker->numerify('IJZ-####/##/####'),
                    'ijazah_file_path'   => $dummyFilePath,
                ]);

                // --- Keluarga: Pasangan ---
                if (in_array($statusPerkawinan, ['Kawin', 'Cerai Hidup', 'Cerai Mati'])) {
                    Pasangan::query()->create([
                        'pegawai_pribadi_id' => $pegawaiPribadi->id,
                        'nama_lengkap'       => $faker->name(),
                        'nik'                => $faker->unique()->numerify('3###############'),
                        'tempat_lahir'       => $faker->city(),
                        'tanggal_lahir'      => $faker->dateTimeBetween('-50 years', '-22 years')->format('Y-m-d'),
                        'pekerjaan'          => $faker->randomElement(['PNS', 'Wiraswasta', 'Karyawan Swasta', 'TNI/Polri', 'Ibu Rumah Tangga', 'Guru']),
                        'instansi'           => $faker->company(),
                        'status_pernikahan'  => $statusPerkawinan === 'Kawin' ? 'Kawin' : 'Cerai',
                        'tanggal_pernikahan' => $faker->dateTimeBetween('-20 years', '-1 years')->format('Y-m-d'),
                        'nomor_buku_nikah'   => $faker->numerify('BKN-####-####'),
                        'status_tanggungan'  => $statusPerkawinan === 'Kawin',
                        'npwp_pasangan'      => $faker->numerify('##.###.###.#-###.###'),
                        'buku_nikah_file_path' => $dummyFilePath,
                    ]);

                    // --- Keluarga: Anak ---
                    $numAnak = $faker->numberBetween(0, 4);
                    for ($a = 0; $a < $numAnak; $a++) {
                        $tglLahirAnak = $faker->dateTimeBetween('-22 years', '-1 years');
                        $usiaAnak     = Carbon::instance($tglLahirAnak)->diffInYears(Carbon::now());
                        $pendidikanAnak = $usiaAnak < 6 ? 'Belum Sekolah' : ($usiaAnak < 12 ? 'SD' : ($usiaAnak < 15 ? 'SMP' : 'SMA'));
                        Anak::query()->create([
                            'pegawai_pribadi_id' => $pegawaiPribadi->id,
                            'nama_lengkap'       => $faker->name(),
                            'nik'                => $faker->unique()->numerify('3###############'),
                            'tempat_lahir'       => $faker->city(),
                            'tanggal_lahir'      => $tglLahirAnak->format('Y-m-d'),
                            'jenis_kelamin'      => $faker->randomElement(['L', 'P']),
                            'status_anak'        => 'Anak Kandung',
                            'pendidikan_terakhir'=> $pendidikanAnak,
                            'status_tanggungan'  => $usiaAnak < 21,
                            'usia'               => $usiaAnak,
                            'keterangan_disabilitas' => '-',
                            'akta_kelahiran_file_path' => $dummyFilePath,
                        ]);
                    }
                }

                // --- Keluarga: Orang Tua ---
                OrangTua::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'nama_ayah'          => $faker->name('male'),
                    'nama_ibu'           => $faker->name('female'),
                    'status_hidup'       => $faker->randomElement(['Hidup', 'Hidup', 'Hidup', 'Meninggal']),
                    'alamat'             => $faker->address(),
                ]);

                // --- Keluarga: Kontak Darurat ---
                KontakDarurat::query()->create([
                    'pegawai_pribadi_id' => $pegawaiPribadi->id,
                    'nama_kontak'        => $faker->name(),
                    'hubungan_keluarga'  => $faker->randomElement(['Suami/Istri', 'Orang Tua', 'Saudara Kandung', 'Anak', 'Paman', 'Bibi']),
                    'nomor_hp'           => '08' . $faker->numerify('##########'),
                    'alamat'             => $faker->address(),
                ]);

                // --- Riwayat Karir: Jabatan ---
                $this->seedRiwayatJabatan($faker, $pegawai, $jabatanId, $jabatanIds, $dummyFilePath);

                // --- Riwayat Karir: Pangkat ---
                $this->seedRiwayatPangkat($faker, $pegawai, $currentEntry, $careerLadder, $ladderIndex, $tmtPangkat);

                // --- Diklat ---
                $this->seedDiklat($faker, $pegawai, $diklatIds, $dummyFilePath, $appStartDate);

                // --- Notifikasi ---
                $this->seedNotifikasi($faker, $user, $appStartDate);
            });

            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info('Berhasil meng-generate ' . self::TOTAL_USERS . ' data pegawai lengkap dengan riwayat karir, diklat, dan notifikasi.');
    }

    private function seedMasterData(mixed $faker): void
    {
        // Unit Kerja
        $unitKerjaList = [
            'Instalasi Rawat Inap', 'Instalasi Gawat Darurat', 'Instalasi Rawat Jalan',
            'Instalasi Bedah Sentral', 'Instalasi ICU/ICCU', 'Instalasi Farmasi',
            'Instalasi Radiologi', 'Instalasi Laboratorium', 'Instalasi Gizi',
            'Instalasi Rehabilitasi Medik', 'Bagian SDM', 'Bagian Keuangan',
            'Bagian Umum', 'Bagian Rekam Medis', 'Sub Bagian IT',
        ];
        foreach ($unitKerjaList as $nama) {
            UnitKerja::query()->firstOrCreate(['nama' => $nama]);
        }

        // Jenis Pegawai
        foreach (['PNS', 'PPPK Penuh Waktu', 'PPPK Paruh Waktu', 'BLUD'] as $nama) {
            JenisPegawai::query()->firstOrCreate(['nama' => $nama]);
        }

        // Profesi
        $profesiList = [
            ['nama' => 'Dokter Umum',      'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Dokter Spesialis', 'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Perawat',          'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Bidan',            'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Apoteker',         'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Analis Kesehatan', 'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Radiografer',      'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Fisioterapis',     'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Nutrisionis',      'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Analis SDM',       'kategori_tenaga' => 'Non Kesehatan'],
            ['nama' => 'Pengelola Keuangan', 'kategori_tenaga' => 'Non Kesehatan'],
            ['nama' => 'Pranata Komputer', 'kategori_tenaga' => 'Non Kesehatan'],
        ];
        foreach ($profesiList as $item) {
            Profesi::query()->firstOrCreate(['nama' => $item['nama']], ['kategori_tenaga' => $item['kategori_tenaga']]);
        }

        // Golongan Ruang
        foreach (array_column(self::PANGKAT_GOLONGAN, 'golongan') as $nama) {
            DB::table('golongan_ruang')->updateOrInsert(['nama' => $nama], ['created_at' => now(), 'updated_at' => now()]);
        }

        // Pangkat
        foreach (array_column(self::PANGKAT_GOLONGAN, 'pangkat') as $nama) {
            Pangkat::query()->firstOrCreate(['nama' => $nama], ['pejabat_penetap' => 'Bupati', 'tmt_sk' => '2015-01-01']);
        }

        // Jabatan
        $unitKerjaIds = UnitKerja::pluck('id', 'nama')->toArray();
        foreach (self::JABATAN_RS as $namaJabatan) {
            $unitKey = $faker->randomElement(array_keys($unitKerjaIds));
            Jabatan::query()->firstOrCreate(
                ['nama' => $namaJabatan],
                ['tmt_mulai' => '2015-01-01', 'unit_kerja_id' => $unitKerjaIds[$unitKey]]
            );
        }

        // Jenis Diklat & Kategori
        foreach (['ASN', 'Tenkes'] as $nama) {
            JenisDiklat::query()->firstOrCreate(['nama' => $nama]);
        }
        foreach (['Struktural', 'Fungsional', 'Teknis', 'Akred'] as $nama) {
            KategoriDiklat::query()->firstOrCreate(['nama' => $nama]);
        }

        // Diklat
        $jenisDiklatMap    = JenisDiklat::pluck('id', 'nama')->toArray();
        $kategoriDiklatMap = KategoriDiklat::pluck('id', 'nama')->toArray();
        $penyelenggara     = [
            'RSUD Dr. Soebandi Jember', 'BBPK Jakarta', 'RSUD Dr. Saiful Anwar Malang',
            'RSUP Dr. Sardjito Yogyakarta', 'Balai Diklat Kementerian Kesehatan',
            'RSUD Banyuwangi', 'Kementerian Kesehatan RI',
        ];
        $baseDate = Carbon::now()->subYear();
        foreach (self::DIKLAT_RS as $idx => $d) {
            $mulai = $baseDate->copy()->addMonths($idx)->addDays($faker->numberBetween(0, 15));
            Diklat::query()->firstOrCreate(
                ['nama_kegiatan' => $d['nama']],
                [
                    'penyelenggara'      => $faker->randomElement($penyelenggara),
                    'tanggal_mulai'      => $mulai->toDateString(),
                    'tanggal_selesai'    => $mulai->copy()->addDays((int)ceil($d['jp'] / 8))->toDateString(),
                    'jp'                 => $d['jp'],
                    'jenis_diklat_id'    => $jenisDiklatMap[$d['jenis']] ?? null,
                    'kategori_diklat_id' => $kategoriDiklatMap[$d['kategori']] ?? null,
                    'jenis_pelaksanaan'  => $faker->randomElement(['internal', 'external']),
                    'tempat'             => $faker->city(),
                ]
            );
        }
    }

    private function buildCareerLadder(): array
    {
        $pangkatMap  = Pangkat::pluck('id', 'nama')->toArray();
        $golonganMap = GolonganRuang::pluck('id', 'nama')->toArray();

        $ladder = [];
        foreach (self::PANGKAT_GOLONGAN as $entry) {
            $pangkatId  = $pangkatMap[$entry['pangkat']] ?? null;
            $golonganId = $golonganMap[$entry['golongan']] ?? null;
            if ($pangkatId && $golonganId) {
                $ladder[] = ['pangkat_id' => $pangkatId, 'golongan_id' => $golonganId, 'label' => $entry['pangkat']];
            }
        }
        return $ladder;
    }

    private function seedRiwayatJabatan(mixed $faker, Pegawai $pegawai, int $currentJabatanId, array $jabatanIds, string $dummyFile): void
    {
        $numRiwayat = $faker->numberBetween(1, 3);
        $cursor     = Carbon::now()->subYears($faker->numberBetween(3, 10));

        for ($r = 0; $r < $numRiwayat; $r++) {
            $isCurrent = ($r === $numRiwayat - 1);
            $startedAt = $cursor->copy()->toDateString();
            $endedAt   = $isCurrent ? null : $cursor->copy()->addMonths($faker->numberBetween(12, 36))->toDateString();

            JabatanPegawai::query()->create([
                'pegawai_id' => $pegawai->id,
                'jabatan_id' => $isCurrent ? $currentJabatanId : $faker->randomElement($jabatanIds),
                'is_current' => $isCurrent,
                'started_at' => $startedAt,
                'ended_at'   => $endedAt,
                'note'       => $isCurrent ? 'Jabatan saat ini' : 'Riwayat jabatan sebelumnya',
            ]);

            if (!$isCurrent) {
                $cursor->addMonths($faker->numberBetween(12, 36));
            }
        }
    }

    private function seedRiwayatPangkat(mixed $faker, Pegawai $pegawai, array $currentEntry, array $careerLadder, int $ladderIndex, string $tmtPangkat): void
    {
        $numPrev = min($ladderIndex, $faker->numberBetween(1, 3));
        $cursor  = Carbon::parse($tmtPangkat)->subYears($numPrev * 4);

        // Previous pangkat entries
        for ($p = $ladderIndex - $numPrev; $p < $ladderIndex; $p++) {
            if (!isset($careerLadder[$p])) {
                continue;
            }
            $startedAt = $cursor->copy()->toDateString();
            $endedAt   = $cursor->copy()->addYears(4)->toDateString();
            PangkatPegawai::query()->create([
                'pegawai_id' => $pegawai->id,
                'pangkat_id' => $careerLadder[$p]['pangkat_id'],
                'is_current' => false,
                'started_at' => $startedAt,
                'ended_at'   => $endedAt,
                'note'       => 'Riwayat pangkat sebelumnya',
            ]);
            $cursor->addYears(4);
        }

        // Current pangkat
        PangkatPegawai::query()->create([
            'pegawai_id' => $pegawai->id,
            'pangkat_id' => $currentEntry['pangkat_id'],
            'is_current' => true,
            'started_at' => $tmtPangkat,
            'ended_at'   => null,
            'note'       => 'Pangkat saat ini',
        ]);
    }

    private function seedDiklat(mixed $faker, Pegawai $pegawai, array $diklatIds, string $dummyFile, Carbon $appStartDate): void
    {
        if (empty($diklatIds)) {
            return;
        }

        $numDiklat  = $faker->numberBetween(1, 5);
        $usedDiklat = [];

        for ($d = 0; $d < $numDiklat; $d++) {
            $diklatId = $faker->randomElement($diklatIds);
            if (in_array($diklatId, $usedDiklat)) {
                continue;
            }
            $usedDiklat[] = $diklatId;

            // Determine timing: past, ongoing, or future
            $rand = $faker->numberBetween(1, 10);
            if ($rand <= 7) {
                // Past — sudah terlaksana
                ListJadwalDiklat::query()->create([
                    'pegawai_id'       => $pegawai->id,
                    'diklat_id'        => $diklatId,
                    'sertif_file_path' => $dummyFile,
                    'no_sertif'        => $faker->numerify('SRT-####/####'),
                    'uploaded_at'      => $faker->dateTimeBetween($appStartDate, 'now'),
                    'status_diklat'    => 'sudah terlaksana',
                    'status_kelayakan' => $faker->randomElement(['layak', 'layak', 'tidak layak']),
                    'status_validasi'  => $faker->randomElement(['valid', 'valid', 'tidak valid']),
                ]);
            } elseif ($rand <= 9) {
                // Ongoing — sedang terlaksana
                ListJadwalDiklat::query()->create([
                    'pegawai_id'       => $pegawai->id,
                    'diklat_id'        => $diklatId,
                    'sertif_file_path' => null,
                    'no_sertif'        => null,
                    'uploaded_at'      => null,
                    'status_diklat'    => 'sedang terlaksana',
                    'status_kelayakan' => null,
                    'status_validasi'  => null,
                ]);
            } else {
                // Upcoming — belum terlaksana
                ListJadwalDiklat::query()->create([
                    'pegawai_id'       => $pegawai->id,
                    'diklat_id'        => $diklatId,
                    'sertif_file_path' => null,
                    'no_sertif'        => null,
                    'uploaded_at'      => null,
                    'status_diklat'    => 'belum terlaksana',
                    'status_kelayakan' => null,
                    'status_validasi'  => null,
                ]);
            }
        }
    }

    private function seedNotifikasi(mixed $faker, User $user, Carbon $appStartDate): void
    {
        $numNotif = $faker->numberBetween(3, 8);
        $types    = array_keys(self::NOTIF_TEMPLATES);

        for ($n = 0; $n < $numNotif; $n++) {
            $type     = $faker->randomElement($types);
            $template = $faker->randomElement(self::NOTIF_TEMPLATES[$type]);
            $createdAt = $faker->dateTimeBetween($appStartDate, 'now');
            $isRead    = $faker->boolean(65); // 65% already read

            NotificationModel::query()->create([
                'user_id'        => $user->id,
                'type'           => $type,
                'title'          => $template['title'],
                'message'        => $template['message'],
                'is_read'        => $isRead,
                'is_resolved'    => $isRead && $faker->boolean(80),
                'action_code'    => null,
                'action_payload' => null,
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ]);
        }
    }
}
