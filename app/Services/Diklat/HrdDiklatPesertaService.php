<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HrdDiklatPesertaService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
        private readonly DiklatStatusResolver $statusResolver,
    ) {}

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

        if (strtolower((string) ($diklat->jenis_pelaksanaan ?? '')) === 'external' && count($pegawaiIds) > 1) {
            throw new InvalidArgumentException('Diklat external hanya boleh memiliki satu peserta pegawai.');
        }

        $tanggalMulai = $diklat->tanggal_mulai ? Carbon::parse($diklat->tanggal_mulai)->startOfDay() : null;
        $tanggalSelesai = $diklat->tanggal_selesai ? Carbon::parse($diklat->tanggal_selesai)->startOfDay() : null;

        $statusDiklat = null;
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $statusDiklat = $this->statusResolver->jadwalStatus($tanggalMulai, $tanggalSelesai);
        }

        DB::transaction(function () use ($diklatId, $pegawaiIds, $statusDiklat) {
            $this->pegawaiDiklatRepository->deleteJadwalNotInPegawaiIds($diklatId, $pegawaiIds);

            $existingIds = $this->pegawaiDiklatRepository->getPesertaDiklatIds($diklatId);
            $newIds = array_diff($pegawaiIds, $existingIds);

            foreach ($newIds as $pegawaiId) {
                $this->pegawaiDiklatRepository->createJadwalDiklat([
                    'diklat_id' => $diklatId,
                    'pegawai_id' => $pegawaiId,
                    'status_diklat' => $statusDiklat,
                    'status_kelayakan' => 'layak',
                    'status_validasi' => null,
                ]);
            }
        });

        return [
            'diklat_id' => $diklatId,
            'peserta_terdaftar' => count($pegawaiIds),
        ];
    }

}
