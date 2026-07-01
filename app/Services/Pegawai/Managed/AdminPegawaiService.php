<?php

namespace App\Services\Pegawai\Managed;

use App\Repositories\Notification\NotificationRepository;
use App\Repositories\Pegawai\Managed\AdminPegawaiRepository;
use Illuminate\Support\Facades\Storage;

class AdminPegawaiService
{
    public function __construct(
        private readonly AdminPegawaiRepository $repository,
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    public function getPegawaiData(array $filters = []): array
    {
        $overviewCounts = $this->repository->getPegawaiOverviewCounts();
        $totalPegawai = $overviewCounts['total_pegawai'];
        $jumlahDokter = $overviewCounts['jumlah_dokter'];
        $jumlahPerawat = $overviewCounts['jumlah_perawat'];
        $jumlahProfesi = $overviewCounts['jumlah_profesi'];

        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 10);
        $paginatedPegawai = $this->repository->getPaginatedPegawai($perPage, $filters);
        
        $mappedData = $paginatedPegawai->getCollection()->map(function ($pegawai) {
            $fotoPath = $pegawai->pribadi?->foto_path;
            $linkPhotoProfil = $fotoPath ? '/'.$fotoPath : null;

            $isLengkap = true;
            $pribadi = $pegawai->pribadi;
            if (!$pribadi) {
                $isLengkap = false;
            } elseif (
                empty($pribadi->tanggal_lahir) || 
                empty($pribadi->jenis_kelamin) || 
                empty($pribadi->agama) || 
                empty($pribadi->alamat) || 
                empty($pribadi->no_telp) || 
                empty($pribadi->pendidikan_terakhir) ||
                empty($pribadi->ktp_file_path) ||
                empty($pribadi->kk_file_path)
            ) {
                $isLengkap = false;
            }

            return [
                'id_pegawai' => $pegawai->id,
                'nama' => $pegawai->nama,
                'nik' => $pegawai->nik,
                'role' => $pegawai->user?->role,
                'link_photo_profil' => $linkPhotoProfil,
                'pendidikan_terakhir' => $pegawai->pribadi?->pendidikan_terakhir,
                'unit_kerja' => $pegawai->jabatan?->unitKerja?->nama,
                'email' => $pegawai->pribadi?->email,
                'no_telp' => $pegawai->pribadi?->no_hp ?? $pegawai->pribadi?->no_telp,
                'status' => $pegawai->status_pegawai,
                'status_kelengkapan' => $isLengkap ? 'Lengkap' : 'Tidak Lengkap',
            ];
        });

        $paginatedPegawai->setCollection($mappedData);

        return [
            'total_pegawai' => $totalPegawai,
            'jumlah_dokter' => $jumlahDokter,
            'jumlah_perawat' => $jumlahPerawat,
            'jumlah_profesi' => $jumlahProfesi,
            'jumlah_pegawai_aktif' => $this->repository->getTotalPegawaiAktif(),
            'jumlah_hrd' => $this->repository->countUsersByRole('hrd'),
            'jumlah_direktur' => $this->repository->countUsersByRole('direktur'),
            'pegawai' => $paginatedPegawai,
        ];
    }

    public function getPegawaiDetailData(int $pegawaiId): array
    {
        $pegawai = $this->repository->getPegawaiDetail($pegawaiId);

        if (!$pegawai) {
            throw new \RuntimeException('Data pegawai tidak ditemukan.');
        }

        $pribadi = $pegawai->pribadi;
        $fotoPath = $pribadi?->foto_path;
        $linkPhotoProfil = $fotoPath ? '/'.$fotoPath : null;

        return [
            'pegawai' => [
                'id_pegawai' => $pegawai->id,
                'nik' => $pegawai->nik,
                'nip' => $pegawai->nip,
                'nama' => $pegawai->nama,
                'email' => $pribadi?->email,
                'link_photo_profil' => $linkPhotoProfil,
                'jabatan' => $pegawai->jabatan?->nama,
                'unit_kerja' => $pegawai->jabatan?->unitKerja?->nama,
                'profesi' => $pegawai->profesi?->nama,
                'golongan_ruang' => $pegawai->golonganRuang?->nama,
                'pangkat' => $pegawai->pangkat?->nama,
                'jenis_pegawai' => $pegawai->jenisPegawai?->nama,
                'status_pegawai' => $pegawai->status_pegawai,
                'tgl_masuk' => $pegawai->tgl_masuk?->format('Y-m-d'),
                'tmt_cpns' => $pegawai->tmt_cpns?->format('Y-m-d'),
                'tmt_pns' => $pegawai->tmt_pns?->format('Y-m-d'),
            ],
            'pribadi' => [
                'jenis_kelamin' => $pribadi?->jenis_kelamin,
                'tanggal_lahir' => $pribadi?->tanggal_lahir?->format('Y-m-d'),
                'agama' => $pribadi?->agama,
                'status_perkawinan' => $pribadi?->status_perkawinan,
                'alamat' => $pribadi?->alamat,
                'no_hp' => $pribadi?->no_hp ?? $pribadi?->no_telp,
                'no_telp' => $pribadi?->no_telp,
                'ktp_file_path' => $pribadi?->ktp_file_path,
                'kk_file_path' => $pribadi?->kk_file_path,
            ],
            'keluarga' => [
                'pasangan' => $pegawai->pasangan?->map(fn($p) => [
                    'id' => $p->id,
                    'nama' => $p->nama_lengkap,
                    'status_hubungan' => $p->status_pernikahan,
                    'pekerjaan' => $p->pekerjaan,
                    'no_hp' => null,
                ]) ?? [],
                'anak' => $pegawai->anak?->map(fn($a) => [
                    'id' => $a->id,
                    'nama' => $a->nama_lengkap,
                    'tanggal_lahir' => $a->tanggal_lahir?->format('Y-m-d'),
                    'pendidikan' => $a->pendidikan_terakhir,
                ]) ?? [],
                'orang_tua' => $pegawai->orangTua?->map(fn($o) => [
                    'id' => $o->id,
                    'nama_ayah' => $o->nama_ayah,
                    'nama_ibu' => $o->nama_ibu,
                    'status_hidup' => $o->status_hidup,
                    'alamat' => $o->alamat,
                ]) ?? [],
                'kontak_darurat' => $pegawai->kontakDarurat?->map(fn($k) => [
                    'id' => $k->id,
                    'nama' => $k->nama_kontak,
                    'hubungan' => $k->hubungan_keluarga,
                    'no_hp' => $k->nomor_hp,
                ]) ?? [],
                'tanggungan_lain' => $pegawai->tanggunganLain?->map(fn($t) => [
                    'id' => $t->id,
                    'nama' => $t->nama,
                    'hubungan' => $t->hubungan_keluarga,
                ]) ?? [],
            ],
            'riwayat_karir' => [
                'pendidikan' => $pegawai->pendidikan?->map(fn($pd) => [
                    'id' => $pd->id,
                    'jenjang' => $pd->jenjang,
                    'institusi' => $pd->institusi,
                    'jurusan' => $pd->jurusan,
                    'tahun_lulus' => $pd->tahun_lulus,
                    'nomor_ijazah' => $pd->nomor_ijazah,
                    'ijazah_file_path' => $pd->ijazah_file_path,
                ]) ?? [],
                'jabatan' => $pegawai->jabatanPegawai?->map(fn($jp) => [
                    'id' => $jp->id,
                    'jabatan' => $jp->jabatan?->nama,
                    'unit_kerja' => $jp->jabatan?->unitKerja?->nama ?? ($jp->is_current ? $pegawai->jabatan?->unitKerja?->nama : null),
                    'tanggal_mulai' => $jp->started_at?->format('Y-m-d'),
                    'tanggal_selesai' => $jp->ended_at?->format('Y-m-d'),
                    'is_current' => (bool)$jp->is_current,
                    'file_path' => $jp->sk_file_path ?? null,
                ]) ?? [],
                'str' => $pegawai->str?->map(fn($s) => [
                    'id' => $s->id,
                    'nomor_str' => $s->nomor_str,
                    'tanggal_terbit' => $s->tanggal_terbit?->format('Y-m-d'),
                    'tanggal_kadaluarsa' => $s->tanggal_kadaluarsa?->format('Y-m-d'),
                    'status' => $this->resolveTanggalStatus($s->tanggal_kadaluarsa),
                    'file_path' => $s->sk_file_path,
                ]) ?? [],
                'sip' => $pegawai->sip?->map(fn($s) => [
                    'id' => $s->id,
                    'jenis_sip' => $s->jenisSip?->nama,
                    'nomor_sip' => $s->nomor_sip,
                    'tanggal_terbit' => $s->tanggal_terbit?->format('Y-m-d'),
                    'tanggal_kadaluarsa' => $s->tanggal_kadaluarsa?->format('Y-m-d'),
                    'status' => $this->resolveTanggalStatus($s->tanggal_kadaluarsa),
                    'file_path' => $s->sk_file_path,
                ]) ?? [],
                'penugasan_klinis' => $pegawai->penugasanKlinis?->map(fn($pk) => [
                    'id' => $pk->id,
                    'nomor_surat' => $pk->nomor_surat,
                    'tanggal_mulai' => $pk->tgl_mulai?->format('Y-m-d'),
                    'tanggal_kadaluarsa' => $pk->tgl_kadaluarsa?->format('Y-m-d'),
                    'status' => $this->resolveTanggalStatus($pk->tgl_kadaluarsa),
                    'file_path' => $pk->dokumen_file_path,
                ]) ?? [],
                'pangkat' => $pegawai->riwayatPangkat?->map(fn($p) => [
                    'id' => $p->id,
                    'pangkat' => $p->pangkat?->nama,
                    'pejabat_penetap' => $p->pangkat?->pejabat_penetap,
                    'tanggal_mulai' => $p->started_at?->format('Y-m-d'),
                    'tanggal_selesai' => $p->ended_at?->format('Y-m-d'),
                    'is_current' => (bool)$p->is_current,
                ]) ?? [],
            ],
            'diklat' => $pegawai->jadwalDiklat?->map(fn($jd) => [
                'id' => $jd->id,
                'nama' => $jd->diklat?->nama_kegiatan,
                'jenis' => $jd->diklat?->jenisDiklat?->nama,
                'kategori' => $jd->diklat?->kategoriDiklat?->nama,
                'penyelenggara' => $jd->diklat?->penyelenggara,
                'tanggal_mulai' => $jd->diklat?->tanggal_mulai?->format('Y-m-d'),
                'tanggal_selesai' => $jd->diklat?->tanggal_selesai?->format('Y-m-d'),
                'waktu' => $jd->diklat?->waktu,
                'created_by' => $jd->diklat?->createdByPegawai?->nama,
                'jp' => $jd->diklat?->jp,
                'total_biaya' => $jd->diklat?->total_biaya,
                'jenis_biaya' => $jd->diklat?->jenisBiaya?->nama,
                'jenis_pelaksana' => $jd->diklat?->jenis_pelaksanaan,
                'catatan' => $jd->diklat?->catatan,
                'sertif' => $jd->sertif_file_path,
                'no_sertif' => $jd->no_sertif,
                'status_diklat' => $jd->status_diklat,
                'status_kelayakan' => $jd->status_kelayakan,
                'status_validasi' => $jd->status_validasi,
            ]) ?? [],
        ];
    }

    public function getPegawaiDetailBagianData(int $pegawaiId, string $bagian, array $filters = []): array
    {
        $payload = $this->getPegawaiDetailData($pegawaiId);
        $bagian = $this->normalizeDetailBagian($bagian);

        if ($bagian === 'diklat') {
            return ['diklat' => $this->filterAndPaginateDiklat($payload['diklat'], $filters)];
        }

        return [$bagian => $payload[$bagian]];
    }

    private function filterAndPaginateDiklat(mixed $diklat, array $filters): array
    {
        $items = collect($diklat);
        $statusKelayakanFilter = $this->filledFilter($filters['status_kelayakan'] ?? $filters['kelayakan'] ?? null);

        if (! $this->includeAllDiklat($filters) && $statusKelayakanFilter === null) {
            $items = $items->reject(fn($item) => $this->isTidakLayakDiklat($item['status_kelayakan'] ?? null));
        }

        $search = $this->filledFilter($filters['search'] ?? null);
        if ($search !== null) {
            $needle = strtolower($search);
            $items = $items->filter(function (array $item) use ($needle): bool {
                $haystack = [
                    $item['nama'] ?? null,
                    $item['jenis'] ?? null,
                    $item['kategori'] ?? null,
                    $item['penyelenggara'] ?? null,
                    $item['created_by'] ?? null,
                    $item['jenis_biaya'] ?? null,
                    $item['jenis_pelaksana'] ?? null,
                    $item['catatan'] ?? null,
                    $item['sertif'] ?? null,
                    $item['no_sertif'] ?? null,
                    $item['status_diklat'] ?? null,
                    $item['status_kelayakan'] ?? null,
                    $item['status_validasi'] ?? null,
                ];

                foreach ($haystack as $value) {
                    if ($value !== null && str_contains(strtolower((string) $value), $needle)) {
                        return true;
                    }
                }

                return false;
            });
        }

        $filterMap = [
            'jenis' => 'jenis',
            'kategori' => 'kategori',
            'status_diklat' => 'status_diklat',
            'status_kelayakan' => 'status_kelayakan',
            'kelayakan' => 'status_kelayakan',
            'status_validasi' => 'status_validasi',
            'jenis_pelaksana' => 'jenis_pelaksana',
            'jenis_pelaksanaan' => 'jenis_pelaksana',
            'jenis_biaya' => 'jenis_biaya',
            'created_by' => 'created_by',
        ];

        foreach ($filterMap as $queryKey => $itemKey) {
            $value = $this->filledFilter($filters[$queryKey] ?? null);
            if ($value === null || in_array(strtolower($value), ['all', 'semua'], true)) {
                continue;
            }

            $items = $items->filter(fn(array $item): bool => str_contains(
                strtolower((string) ($item[$itemKey] ?? '')),
                strtolower($value)
            ));
        }

        $items = $items->values();
        $total = $items->count();
        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 10);
        $page = max(1, (int) ($filters['page'] ?? 1));
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $pageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return [
            'current_page' => $page,
            'data' => $pageItems,
            'from' => $total === 0 ? null : (($page - 1) * $perPage) + 1,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'to' => $total === 0 ? null : (($page - 1) * $perPage) + $pageItems->count(),
            'total' => $total,
        ];
    }

    private function includeAllDiklat(array $filters): bool
    {
        $all = $this->filledFilter($filters['all'] ?? null);
        $kelayakan = $this->filledFilter($filters['kelayakan'] ?? $filters['status_kelayakan'] ?? null);

        return in_array(strtolower((string) $all), ['1', 'true', 'yes', 'all', 'semua'], true)
            || in_array(strtolower((string) $kelayakan), ['all', 'semua'], true);
    }

    private function isTidakLayakDiklat(mixed $value): bool
    {
        if ($value === false || $value === 0) {
            return true;
        }

        return in_array(strtolower(trim((string) $value)), ['tidak layak', 'false', '0'], true);
    }

    private function filledFilter(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeDetailBagian(string $bagian): string
    {
        $normalized = str_replace('-', '_', strtolower(trim($bagian)));
        $allowed = ['pegawai', 'keluarga', 'riwayat_karir', 'diklat'];

        if (! in_array($normalized, $allowed, true)) {
            throw new \InvalidArgumentException('Endpoint detail pegawai tidak valid. Gunakan salah satu: pegawai, keluarga, riwayat-karir, diklat.');
        }

        return $normalized;
    }

    public function createPegawai(array $data): array
    {
        $pegawai = $this->repository->createPegawaiData($data);

        if ((int) ($pegawai->user_id ?? 0) > 0) {
            $this->notificationRepository->createInfo(
                (int) $pegawai->user_id,
                'Selamat Datang di SIMPEG RSKalisat',
                "Halo {$pegawai->nama}, akun Anda telah dibuat. Silakan lengkapi data profil Anda untuk memulai."
            );
        }

        return [
            'id' => $pegawai->id,
            'nik' => $pegawai->nik,
            'nama' => $pegawai->nama,
        ];
    }

    public function changeRole(int $pegawaiId, array $data, int $adminUserId): array
    {
        $pegawai = $this->repository->getAllPegawai()->where('id', $pegawaiId)->first();
        if (!$pegawai) {
            throw new \RuntimeException('Pegawai tidak ditemukan.');
        }

        if ($pegawai->user_id === $adminUserId) {
            throw new \RuntimeException('Tidak dapat mengubah role/status diri sendiri.');
        }

        $updatedPegawai = $this->repository->changeRole($pegawaiId, $data);

        return [
            'id' => $updatedPegawai->id,
            'nik' => $updatedPegawai->nik,
            'nama' => $updatedPegawai->nama,
            'role' => $updatedPegawai->user?->role,
            'status_pegawai' => $updatedPegawai->status_pegawai,
        ];
    }

    private function resolvePerPage(mixed $value, int $default): int
    {
        $perPage = is_numeric($value) ? (int) $value : $default;

        return max(1, min($perPage, 100));
    }

    private function resolveTanggalStatus(mixed $tanggalKadaluarsa): string
    {
        if ($tanggalKadaluarsa === null || $tanggalKadaluarsa === '') {
            return 'aktif';
        }

        return \Carbon\Carbon::parse($tanggalKadaluarsa)->lt(\Carbon\Carbon::today())
            ? 'tidak aktif'
            : 'aktif';
    }
}
