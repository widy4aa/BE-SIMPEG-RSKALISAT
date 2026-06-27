<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HrdService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
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

        $riwayat = $paginatedRiwayat->getCollection()->map(function ($jadwal): array {
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
                'status' => $this->resolveStatusByTanggal($tanggalMulai, $tanggalSelesai),
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
                    $this->resolveStatusByTanggal($tanggalMulai, $tanggalSelesai)
                ),
            ];
        });

        $paginatedRiwayat->setCollection($riwayat);

        return [
            'welcome' => 'Daftar diklat untuk HRD berhasil diambil.',
            'summary' => [
                'label' => 'Diklat hrd',
                'ringkasan' => [
                    'total_riwayat' => $totalRiwayat,
                    'selesai' => $selesai,
                    'akan_datang' => $akanDatang,
                ],
                'riwayat_diklat' => $paginatedRiwayat,
            ],
        ];
    }

    public function getAllDiklat(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 7);
        $paginatedDiklat = $this->pegawaiDiklatRepository->getPaginatedMasterDiklat($perPage, $filters);

        $items = $paginatedDiklat->getCollection()->map(function ($diklat): array {
            $tanggalMulai = $diklat->tanggal_mulai;
            $tanggalSelesai = $diklat->tanggal_selesai;

            return [
                'id_diklat' => (int) $diklat->id,
                'nama' => (string) ($diklat->nama_kegiatan ?? ''),
                'kategori' => (string) ($diklat->kategoriDiklat?->nama ?? ''),
                'jenis' => (string) ($diklat->jenisDiklat?->nama ?? ''),
                'pelaksana' => (string) ($diklat->penyelenggara ?? ''),
                'tanggal_mulai' => optional($tanggalMulai)?->toDateString(),
                'tanggal_selesai' => optional($tanggalSelesai)?->toDateString(),
                'status' => $this->resolveStatusByTanggal($tanggalMulai, $tanggalSelesai),
                'tempat' => (string) ($diklat->tempat ?? ''),
                'waktu' => optional($diklat->waktu)?->format('H:i:s'),
                'created_by' => (string) ($diklat->createdByPegawai?->nama ?? ''),
                'jp' => $diklat->jp,
                'total_biaya' => $diklat->total_biaya,
                'jenis_biaya' => (string) ($diklat->jenisBiaya?->nama ?? ''),
                'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
                'catatan' => (string) ($diklat->catatan ?? ''),
                'jumlah_peserta' => $diklat->jadwal_peserta_count ?? 0,
            ];
        });

        $paginatedDiklat->setCollection($items);

        return $paginatedDiklat;
    }

    public function getPesertaDiklat(int $diklatId): array
    {
        $pesertaIds = $this->pegawaiDiklatRepository->getPesertaDiklatIds($diklatId);
        $semuaPegawai = $this->pegawaiDiklatRepository->getAllPegawaiWithUnitKerjaProfesi();

        $peserta = $semuaPegawai->map(function ($pegawai) use ($pesertaIds): array {
            return [
                'pegawai_id' => (int) $pegawai->id,
                'nama' => (string) $pegawai->nama,
                'nik' => (string) $pegawai->nik,
                'unit_kerja' => (string) ($pegawai->jabatan?->unitKerja?->nama ?? ''),
                'profesi' => (string) ($pegawai->profesi?->nama ?? ''),
                'status' => in_array($pegawai->id, $pesertaIds, true),
            ];
        })->values()->all();

        return [
            'diklat_id' => $diklatId,
            'total_pegawai' => count($peserta),
            'list' => $peserta,
        ];
    }

    public function syncPesertaDiklat(int $diklatId, array $pegawaiIds): array
    {
        $diklat = $this->pegawaiDiklatRepository->findDiklatById($diklatId);
        if ($diklat === null) {
            throw new InvalidArgumentException('Master Diklat tidak ditemukan.');
        }

        $tanggalMulai = $diklat->tanggal_mulai ? Carbon::parse($diklat->tanggal_mulai)->startOfDay() : null;
        $tanggalSelesai = $diklat->tanggal_selesai ? Carbon::parse($diklat->tanggal_selesai)->startOfDay() : null;

        $statusDiklat = null;
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $statusDiklat = $this->resolveStatusDiklatByTanggal($tanggalMulai, $tanggalSelesai);
        }

        DB::transaction(function () use ($diklatId, $pegawaiIds, $statusDiklat) {
            // Delete those not in the new array
            $this->pegawaiDiklatRepository->deleteJadwalNotInPegawaiIds($diklatId, $pegawaiIds);

            // Fetch existing so we don't duplicate
            $existingIds = $this->pegawaiDiklatRepository->getPesertaDiklatIds($diklatId);

            $newIds = array_diff($pegawaiIds, $existingIds);

            foreach ($newIds as $pegawaiId) {
                $this->pegawaiDiklatRepository->createJadwalDiklat([
                    'diklat_id' => $diklatId,
                    'pegawai_id' => $pegawaiId,
                    'status_diklat' => $statusDiklat,
                    'status_kelayakan' => 'layak',
                    'status_validasi' => null, // or 'valid' if needed, but standard is null until validation
                ]);
            }
        });

        return [
            'diklat_id' => $diklatId,
            'peserta_terdaftar' => count($pegawaiIds),
        ];
    }

    private function resolveStatusByTanggal(mixed $tanggalMulai, mixed $tanggalSelesai): string
    {
        $today = Carbon::today();

        $mulai = $tanggalMulai instanceof Carbon
            ? $tanggalMulai->copy()->startOfDay()
            : ($tanggalMulai ? Carbon::parse($tanggalMulai)->startOfDay() : null);

        $selesai = $tanggalSelesai instanceof Carbon
            ? $tanggalSelesai->copy()->startOfDay()
            : ($tanggalSelesai ? Carbon::parse($tanggalSelesai)->startOfDay() : null);

        if ($mulai !== null && $today->lt($mulai)) {
            return 'mendatang';
        }

        if ($selesai !== null && $today->gt($selesai)) {
            return 'selesai';
        }

        return 'berlangsung';
    }

    private function resolvePerPage(mixed $value, int $default): int
    {
        $perPage = is_numeric($value) ? (int) $value : $default;

        return max(1, min($perPage, 100));
    }

    private function resolveStatusDiklatByTanggal(Carbon $tanggalMulai, Carbon $tanggalSelesai): string
    {
        $today = Carbon::today();

        if ($today->lt($tanggalMulai)) {
            return 'belum terlaksana';
        }

        if ($today->gt($tanggalSelesai)) {
            return 'sudah terlaksana';
        }

        return 'sedang terlaksana';
    }

    private function resolveStatusValidasiText(?string $jenisPelaksana, ?string $sertifFilePath, ?string $statusValidasi): ?string
    {
        if (strtolower((string) $jenisPelaksana) !== 'internal') {
            return 'None';
        }

        if ($sertifFilePath === null || $sertifFilePath === '') {
            return 'Belum upload laporan';
        }

        if ($statusValidasi === null || $statusValidasi === '') {
            return 'udah upload laporan namun belum di validasi';
        }

        if ($statusValidasi === 'tidak valid') {
            return 'Validasi di tolak';
        }

        return 'sudah di validasi';
    }

    private function shouldUploadLaporan(?string $jenisPelaksana, ?string $sertifFilePath, ?string $noSertif, ?string $statusValidasi, string $statusPelaksanaan): bool
    {
        if ($statusPelaksanaan !== 'selesai') {
            return false;
        }

        $jenisPelaksana = strtolower(trim((string) $jenisPelaksana));
        $statusValidasi = strtolower(trim((string) $statusValidasi));
        $hasMissingLaporan = $this->hasMissingLaporan($sertifFilePath, $noSertif);

        if ($jenisPelaksana === 'external') {
            return $hasMissingLaporan;
        }

        if ($jenisPelaksana === 'internal') {
            return $hasMissingLaporan || in_array($statusValidasi, ['', 'tidak valid', 'di tolak', 'pending'], true);
        }

        return $hasMissingLaporan;
    }

    private function hasMissingLaporan(?string $sertifFilePath, ?string $noSertif): bool
    {
        return trim((string) $sertifFilePath) === '' || trim((string) $noSertif) === '';
    }

}
