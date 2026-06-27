<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PegawaiService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
        private readonly DiklatStatusResolver $statusResolver,
    ) {
    }

    public function build(int $userId, array $filters = []): array
    {
        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 7);

        if ($pegawai === null) {
            $totalRiwayat = 0;
            $selesai = 0;
            $akanDatang = 0;
            $paginatedRiwayat = new LengthAwarePaginator(collect(), 0, $perPage);
        } else {
            $summary = $this->pegawaiDiklatRepository->getJadwalSummaryByPegawaiId((int) $pegawai->id);
            $totalRiwayat = $summary['total_riwayat'];
            $selesai = $summary['selesai'];
            $akanDatang = $summary['akan_datang'];

            $paginatedRiwayat = $this->pegawaiDiklatRepository->getPaginatedRiwayatDiklatByPegawaiId((int) $pegawai->id, $perPage, $filters);
        }

        $mappedData = $paginatedRiwayat->getCollection()->map(function ($jadwal): array {
            $diklat = $jadwal->diklat;
            $tanggalMulai = $diklat?->tanggal_mulai;
            $tanggalSelesai = $diklat?->tanggal_selesai;

            return [
                'id' => (int) ($diklat?->id ?? $jadwal->id),
                'nama' => (string) ($diklat?->nama_kegiatan ?? ''),
                'kategori' => (string) ($diklat?->kategoriDiklat?->nama ?? ''),
                'jenis' => (string) ($diklat?->jenisDiklat?->nama ?? ''),
                'pelaksana' => (string) ($diklat?->penyelenggara ?? ''),
                'tanggal_mulai' => optional($tanggalMulai)?->toDateString(),
                'tanggal_selesai' => optional($tanggalSelesai)?->toDateString(),
                'status' => $this->statusResolver->displayStatus($tanggalMulai, $tanggalSelesai),
                'tempat' => (string) ($diklat?->tempat ?? ''),
                'waktu' => optional($diklat?->waktu)?->format('H:i:s'),
                'created_by' => (string) ($diklat?->createdByPegawai?->nama ?? ''),
                'jp' => $diklat?->jp,
                'total_biaya' => $diklat?->total_biaya,
                'jenis_biaya' => (string) ($diklat?->jenisBiaya?->nama ?? ''),
                'jenis_pelaksana' => (string) ($diklat?->jenis_pelaksanaan ?? ''),
                'catatan' => (string) ($diklat?->catatan ?? ''),
                'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
                'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
                'status_validasi' => $this->resolveStatusValidasiText(
                    $diklat?->jenis_pelaksanaan,
                    $jadwal->sertif_file_path,
                    $jadwal->status_validasi
                ),
                'uploadlaporan' => $this->shouldUploadLaporan(
                    $diklat?->jenis_pelaksanaan,
                    $jadwal->sertif_file_path,
                    $jadwal->no_sertif,
                    $jadwal->status_validasi,
                    $this->statusResolver->displayStatus($tanggalMulai, $tanggalSelesai)
                ),
            ];
        });

        $paginatedRiwayat->setCollection($mappedData);

        return [
            'welcome' => 'Daftar diklat pegawai berhasil diambil.',
            'summary' => [
                'label' => 'Diklat pegawai',
                'ringkasan' => [
                    'total_riwayat' => $totalRiwayat,
                    'selesai' => $selesai,
                    'akan_datang' => $akanDatang,
                ],
                'riwayat_diklat' => $paginatedRiwayat,
                'catatan' => 'Data diklat diambil dari database untuk role pegawai.',
            ],
        ];
    }

    private function resolvePerPage(mixed $value, int $default): int
    {
        $perPage = is_numeric($value) ? (int) $value : $default;

        return max(1, min($perPage, 100));
    }

    private function resolveStatusValidasiText(?string $jenisPelaksana, ?string $sertifFilePath, ?string $statusValidasi): ?string
    {
        $jenisPelaksana = strtolower((string) $jenisPelaksana);
        if ($jenisPelaksana !== 'internal') {
            return 'None';
        }

        if (empty($sertifFilePath)) {
            return 'Belum upload laporan';
        }

        if ($statusValidasi === null) {
            return 'udah upload laporan namun belum di validasi';
        }

        $statusValidasi = strtolower($statusValidasi);
        if ($statusValidasi === 'tidak valid') {
            return 'Validasi di tolak';
        }

        if ($statusValidasi === 'valid') {
            return 'sudah di validasi';
        }

        return null;
    }

    private function shouldUploadLaporan(?string $jenisPelaksana, ?string $sertifFilePath, ?string $noSertif, ?string $statusValidasi, string $statusPelaksanaan): bool
    {
        if ($statusPelaksanaan !== 'selesai') {
            return false;
        }

        $jenisPelaksana = strtolower(trim((string) $jenisPelaksana));
        $statusValidasi = strtolower(trim((string) $statusValidasi));
        $hasMissingLaporan = trim((string) $sertifFilePath) === '' || trim((string) $noSertif) === '';

        if ($jenisPelaksana === 'external') {
            return $hasMissingLaporan;
        }

        if ($jenisPelaksana === 'internal') {
            return $hasMissingLaporan || in_array($statusValidasi, ['', 'tidak valid', 'di tolak', 'pending'], true);
        }

        return $hasMissingLaporan;
    }
}
