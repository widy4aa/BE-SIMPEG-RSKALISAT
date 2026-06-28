<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class PegawaiDiklatLaporanService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
        private readonly PegawaiDiklatFileService $fileService,
        private readonly PegawaiDiklatResponseMapper $responseMapper,
        private readonly DiklatStatusResolver $statusResolver,
    ) {}

    public function uploadLaporan(int $diklatId, int $userId, array $payload, ?UploadedFile $laporanFile = null): array
    {
        if ($diklatId <= 0) {
            throw new InvalidArgumentException('ID diklat tidak valid.');
        }

        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $jadwal = $this->pegawaiDiklatRepository->findJadwalByDiklatIdAndPegawaiId($diklatId, (int) $pegawai->id);
        if ($jadwal === null || $jadwal->diklat === null) {
            throw new InvalidArgumentException('Data diklat tidak ditemukan atau bukan milik pegawai login.');
        }

        $diklat = $jadwal->diklat;
        $jenisPelaksanaCurrent = strtolower((string) ($diklat->jenis_pelaksanaan ?? ''));

        if ($jenisPelaksanaCurrent === 'internal' && (string) ($jadwal->status_validasi ?? '') === 'valid') {
            throw new InvalidArgumentException('Laporan tidak bisa diupload/diedit karena status validasi sudah valid.');
        }

        if ($jenisPelaksanaCurrent === 'external' && (string) ($jadwal->status_kelayakan ?? '') === 'layak') {
            throw new InvalidArgumentException('Sertifikat tidak bisa diupload/diedit karena status kelayakan sudah layak.');
        }

        $diklat = $jadwal->diklat;
        $statusPelaksanaan = $this->statusResolver->displayStatus($diklat->tanggal_mulai, $diklat->tanggal_selesai);
        if ($statusPelaksanaan !== 'selesai') {
            throw new InvalidArgumentException('Laporan hanya bisa diupload setelah diklat selesai.');
        }

        if (array_key_exists('no_sertif', $payload)) {
            $jadwal->no_sertif = (string) ($payload['no_sertif'] ?? '');
        }

        if ($laporanFile !== null) {
            $jadwal->sertif_file_path = $this->fileService->storeSertifikat((int) $pegawai->id, $laporanFile);
            $jadwal->uploaded_at = now();
        }

        if ($jenisPelaksanaCurrent === 'internal') {
            $jadwal->status_validasi = null;
        } else {
            $jadwal->status_kelayakan = null;
        }

        $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);

        return $this->responseMapper->laporanUploaded($diklat, $jadwal);
    }

}
