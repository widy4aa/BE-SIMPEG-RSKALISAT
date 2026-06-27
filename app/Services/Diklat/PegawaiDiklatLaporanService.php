<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class PegawaiDiklatLaporanService
{
    public function __construct(private readonly PegawaiDiklatRepository $pegawaiDiklatRepository) {}

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

        if ((string) ($jadwal->status_validasi ?? '') === 'valid') {
            throw new InvalidArgumentException('Laporan tidak bisa diupload/diedit karena status validasi sudah valid.');
        }

        $diklat = $jadwal->diklat;
        $statusPelaksanaan = $this->resolveStatusByTanggal($diklat->tanggal_mulai, $diklat->tanggal_selesai);
        if ($statusPelaksanaan !== 'selesai') {
            throw new InvalidArgumentException('Laporan hanya bisa diupload setelah diklat selesai.');
        }

        if (array_key_exists('no_sertif', $payload)) {
            $jadwal->no_sertif = (string) ($payload['no_sertif'] ?? '');
        }

        if ($laporanFile !== null) {
            $folder = public_path('dokumen/sertif-diklat');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sertif-%d-%d.%s',
                (int) $pegawai->id,
                time(),
                $laporanFile->getClientOriginalExtension()
            );

            $laporanFile->move($folder, $filename);
            $jadwal->sertif_file_path = 'dokumen/sertif-diklat/'.$filename;
            $jadwal->uploaded_at = now();
        }

        $jenisPelaksanaCurrent = (string) ($diklat->jenis_pelaksanaan ?? '');

        if ($jenisPelaksanaCurrent === 'internal') {
            $jadwal->status_validasi = null;
        }

        $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);

        return [
            'id_diklat' => (int) $diklat->id,
            'id_jadwal_diklat' => (int) $jadwal->id,
            'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
            'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
            'status_validasi' => $jadwal->status_validasi,
            'uploaded_at' => $jadwal->uploaded_at?->toDateTimeString(),
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
}
