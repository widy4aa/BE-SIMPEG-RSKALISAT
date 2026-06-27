<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;

class DirekturService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $repository,
        private readonly DiklatStatusResolver $statusResolver,
    ) {}

    public function build(int $userId, array $filters = []): array
    {
        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 7);
        $stats = $this->repository->getMasterDiklatStats($filters);
        $paginatedDiklat = $this->repository->getPaginatedMasterDiklat($perPage, $filters);

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
                'status' => $this->statusResolver->displayStatus($tanggalMulai, $tanggalSelesai),
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

        return [
            'welcome' => 'Ringkasan diklat direktur berhasil diambil.',
            'summary' => [
                'label' => 'Diklat direktur',
                'ringkasan' => [
                    'total_diklat' => $stats['total'],
                    'selesai' => $stats['selesai'],
                    'berlangsung' => $stats['berlangsung'],
                    'mendatang' => $stats['mendatang'],
                ],
                'list_diklat' => $paginatedDiklat,
            ],
        ];
    }

    private function resolvePerPage(mixed $value, int $default): int
    {
        $perPage = is_numeric($value) ? (int) $value : $default;

        return max(1, min($perPage, 100));
    }
}
