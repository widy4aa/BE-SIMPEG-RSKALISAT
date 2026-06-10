<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\AdminPegawaiRepository;
use Illuminate\Support\Facades\Storage;

class DirekturPegawaiService
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

    private function resolvePerPage(mixed $value, int $default): int
    {
        $perPage = is_numeric($value) ? (int) $value : $default;

        return max(1, min($perPage, 100));
    }
}
