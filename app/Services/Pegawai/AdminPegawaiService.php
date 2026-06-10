<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\AdminPegawaiRepository;
use Illuminate\Support\Facades\Storage;

class AdminPegawaiService
{
    public function __construct(
        private readonly AdminPegawaiRepository $repository
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
            if (empty($pegawai->nik) || empty($pegawai->jenis_pegawai_id) || empty($pegawai->profesi_id) || empty($pegawai->tgl_masuk)) {
                $isLengkap = false;
            } else {
                $pribadi = $pegawai->pribadi;
                if (!$pribadi) {
                    $isLengkap = false;
                } elseif (empty($pribadi->tanggal_lahir) || empty($pribadi->jenis_kelamin) || empty($pribadi->agama) || empty($pribadi->alamat) || empty($pribadi->no_telp) || empty($pribadi->pendidikan_terakhir)) {
                    $isLengkap = false;
                }
            }

            return [
                'id_pegawai' => $pegawai->id,
                'nama' => $pegawai->nama,
                'nik' => $pegawai->nik,
                'role' => $pegawai->user?->role,
                'link_photo_profil' => $linkPhotoProfil,
                'jabatan' => $pegawai->jabatan?->nama,
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
            'jumlah_admin' => $this->repository->countUsersByRole('admin'),
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
                'tempat_lahir' => $pribadi?->tempat_lahir,
                'tanggal_lahir' => $pribadi?->tanggal_lahir?->format('Y-m-d'),
                'agama' => $pribadi?->agama,
                'status_perkawinan' => $pribadi?->status_perkawinan,
                'alamat' => $pribadi?->alamat,
                'no_hp' => $pribadi?->no_hp ?? $pribadi?->no_telp,
                'no_telp' => $pribadi?->no_telp,
                'npwp' => $pribadi?->npwp,
                'bpjs_kesehatan' => $pribadi?->bpjs_kesehatan,
                'bpjs_ketenagakerjaan' => $pribadi?->bpjs_ketenagakerjaan,
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
                'jabatan' => $pegawai->jabatanPegawai?->map(fn($jp) => [
                    'id' => $jp->id,
                    'jabatan' => $jp->jabatan?->nama,
                    'unit_kerja' => $jp->jabatan?->unitKerja?->nama ?? ($jp->is_current ? $pegawai->jabatan?->unitKerja?->nama : null),
                    'tanggal_mulai' => $jp->started_at?->format('Y-m-d'),
                    'tanggal_selesai' => $jp->ended_at?->format('Y-m-d'),
                    'is_current' => (bool)$jp->is_current,
                ]) ?? [],
                'str' => $pegawai->str?->map(fn($s) => [
                    'id' => $s->id,
                    'nomor_str' => $s->nomor_str,
                    'tanggal_terbit' => $s->tanggal_terbit?->format('Y-m-d'),
                    'tanggal_kadaluarsa' => $s->tanggal_kadaluarsa?->format('Y-m-d'),
                    'is_current' => (bool)$s->is_current,
                ]) ?? [],
                'sip' => $pegawai->sip?->map(fn($s) => [
                    'id' => $s->id,
                    'jenis_sip' => $s->jenisSip?->nama,
                    'nomor_sip' => $s->nomor_sip,
                    'tanggal_terbit' => $s->tanggal_terbit?->format('Y-m-d'),
                    'tanggal_kadaluarsa' => $s->tanggal_kadaluarsa?->format('Y-m-d'),
                    'is_current' => (bool)$s->is_current,
                ]) ?? [],
                'penugasan_klinis' => $pegawai->penugasanKlinis?->map(fn($pk) => [
                    'id' => $pk->id,
                    'nomor_surat' => $pk->nomor_surat,
                    'tanggal_mulai' => $pk->tgl_mulai?->format('Y-m-d'),
                    'tanggal_kadaluarsa' => $pk->tgl_kadaluarsa?->format('Y-m-d'),
                    'is_current' => (bool)$pk->is_current,
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
                'jp' => $jd->diklat?->jp,
                'status_diklat' => $jd->status_diklat,
            ]) ?? [],
        ];
    }

    public function createPegawai(array $data): array
    {
        $pegawai = $this->repository->createPegawaiData($data);

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
}
