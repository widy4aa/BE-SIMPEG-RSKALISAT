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

    public function getPesertaDiklat(int $diklatId, ?string $section = null): array
    {
        $diklat = $this->pegawaiDiklatRepository->findDiklatById($diklatId);
        if ($diklat === null) {
            throw new InvalidArgumentException('Master Diklat tidak ditemukan.');
        }

        $pesertaIds = $this->pegawaiDiklatRepository->getPesertaDiklatIds($diklatId);
        $jadwalByPegawaiId = $this->pegawaiDiklatRepository
            ->getJadwalPesertaDiklatByDiklatId($diklatId)
            ->keyBy(fn ($jadwal) => (int) $jadwal->pegawai_id);
        $semuaPegawai = $this->pegawaiDiklatRepository->getAllPegawaiWithUnitKerjaProfesi();

        $peserta = $semuaPegawai->map(function ($pegawai) use ($pesertaIds, $jadwalByPegawaiId, $diklat): array {
            $jadwal = $jadwalByPegawaiId->get((int) $pegawai->id);

            return [
                'pegawai_id' => (int) $pegawai->id,
                'nama' => (string) $pegawai->nama,
                'nik' => (string) $pegawai->nik,
                'unit_kerja' => (string) ($pegawai->jabatan?->unitKerja?->nama ?? ''),
                'profesi' => (string) ($pegawai->profesi?->nama ?? ''),
                'status' => in_array($pegawai->id, $pesertaIds, true),
                'status_validasi' => $jadwal === null ? null : $this->resolveStatusValidasiText(
                    $diklat->jenis_pelaksanaan,
                    $jadwal->sertif_file_path,
                    $jadwal->status_validasi
                ),
            ];
        })->values()->all();

        $pesertaTerdaftar = array_values(array_filter($peserta, fn ($p) => $p['status'] === true));

        $sectionClean = strtolower(trim((string) $section));
        $showAll = ($sectionClean !== '' && !in_array($sectionClean, ['terdaftar', 'peserta', 'registered'], true));

        $selectedList = $showAll ? $peserta : $pesertaTerdaftar;

        return [
            'diklat_id' => $diklatId,
            'total_peserta' => count($pesertaTerdaftar),
            'total_pegawai' => count($peserta),
            'list' => $selectedList,
        ];
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

        if ($statusValidasi === null || $statusValidasi === '') {
            return 'udah upload laporan namun belum di validasi';
        }

        $statusValidasi = strtolower($statusValidasi);
        if ($statusValidasi === 'tidak valid') {
            return 'Validasi di tolak';
        }

        if ($statusValidasi === 'valid') {
            return 'sudah di validasi';
        }

        return 'udah upload laporan namun belum di validasi';
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
