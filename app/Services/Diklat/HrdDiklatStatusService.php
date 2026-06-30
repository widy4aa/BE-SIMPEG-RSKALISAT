<?php

namespace App\Services\Diklat;

use App\Models\Pegawai;
use App\Repositories\Diklat\PegawaiDiklatRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Services\Notification\WhatsappService;
use InvalidArgumentException;

class HrdDiklatStatusService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
        private readonly WhatsappService $whatsapp,
        private readonly NotificationRepository $notificationRepository,
    ) {}

    public function getDiklatMenungguKelayakan(): array
    {
        $jadwalList = $this->pegawaiDiklatRepository->getJadwalDiklatMenungguKelayakan();

        $items = $jadwalList->map(fn ($jadwal): array => $this->formatJadwalStatus($jadwal))->values()->all();

        return [
            'total' => count($items),
            'list' => $items,
        ];
    }

    public function getDiklatMenungguValidasi(): array
    {
        $jadwalList = $this->pegawaiDiklatRepository->getJadwalDiklatMenungguValidasi();

        $items = $jadwalList->map(fn ($jadwal): array => $this->formatJadwalStatus($jadwal))->values()->all();

        return [
            'total' => count($items),
            'list' => $items,
        ];
    }

    public function updateStatusKelayakan(int $jadwalId, bool $isLayak): array
    {
        $jadwal = $this->pegawaiDiklatRepository->findJadwalById($jadwalId);
        if ($jadwal === null) {
            throw new InvalidArgumentException('Jadwal diklat tidak ditemukan.');
        }

        if (
            $isLayak
            && strtolower((string) ($jadwal->diklat?->jenis_pelaksanaan ?? '')) === 'external'
            && $this->hasMissingLaporan($jadwal->sertif_file_path, $jadwal->no_sertif)
        ) {
            throw new InvalidArgumentException('belum upload laporan');
        }

        $jadwal->status_kelayakan = $isLayak ? 'layak' : 'tidak layak';
        $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);

        return [
            'id_jadwal_diklat' => (int) $jadwal->id,
            'diklat_id'        => (int) ($jadwal->diklat_id ?? 0),
            'pegawai_id'       => (int) ($jadwal->pegawai_id ?? 0),
            'status_kelayakan' => (string) ($jadwal->status_kelayakan ?? ''),
        ];
    }

    public function updateStatusValidasi(int $jadwalId, bool $isValid): array
    {
        $jadwal = $this->pegawaiDiklatRepository->findJadwalById($jadwalId);
        if ($jadwal === null) {
            throw new InvalidArgumentException('Jadwal diklat tidak ditemukan.');
        }

        if (
            $isValid
            && strtolower((string) ($jadwal->diklat?->jenis_pelaksanaan ?? '')) === 'internal'
            && $this->hasMissingLaporan($jadwal->sertif_file_path, $jadwal->no_sertif)
        ) {
            throw new InvalidArgumentException('belum upload laporan');
        }

        $jadwal->status_validasi = $isValid ? 'valid' : 'tidak valid';
        $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);

        $namaDiklat = (string) ($jadwal->diklat?->nama_kegiatan ?? 'Diklat');
        $pesanStatus = $isValid
            ? "✅ Laporan diklat *{$namaDiklat}* Anda telah *DIVALIDASI* oleh HRD."
            : "❌ Laporan diklat *{$namaDiklat}* Anda *DITOLAK*. Harap koordinasi dengan HRD untuk revisi.";
        $this->sendNotifDiklatWa((int) ($jadwal->pegawai_id ?? 0), $namaDiklat, $pesanStatus);

        // Notifikasi in-app
        $pegawai = Pegawai::find((int) ($jadwal->pegawai_id ?? 0));
        $userIdPegawai = (int) ($pegawai?->user_id ?? 0);
        if ($userIdPegawai > 0) {
            if ($isValid) {
                $this->notificationRepository->createInfo(
                    $userIdPegawai,
                    'Laporan Diklat Divalidasi',
                    "Laporan diklat '{$namaDiklat}' Anda telah divalidasi oleh HRD."
                );
            } else {
                $this->notificationRepository->createInfo(
                    $userIdPegawai,
                    'Laporan Diklat Ditolak',
                    "Laporan diklat '{$namaDiklat}' Anda ditolak. Harap koordinasi dengan HRD untuk revisi."
                );
            }
        }

        return [
            'id_jadwal_diklat' => (int) $jadwal->id,
            'diklat_id' => (int) ($jadwal->diklat_id ?? 0),
            'pegawai_id' => (int) ($jadwal->pegawai_id ?? 0),
            'status_validasi' => (string) ($jadwal->status_validasi ?? ''),
        ];
    }

    private function formatJadwalStatus(object $jadwal): array
    {
        $diklat = $jadwal->diklat;
        $tanggalMulai = $diklat?->tanggal_mulai;
        $tanggalSelesai = $diklat?->tanggal_selesai;

        return [
            'id_diklat' => (int) ($diklat?->id ?? 0),
            'id_jadwal_diklat' => (int) $jadwal->id,
            'nama' => (string) ($diklat?->nama_kegiatan ?? ''),
            'kategori' => (string) ($diklat?->kategoriDiklat?->nama ?? ''),
            'jenis' => (string) ($diklat?->jenisDiklat?->nama ?? ''),
            'pelaksana' => (string) ($diklat?->penyelenggara ?? ''),
            'tanggal_mulai' => optional($tanggalMulai)?->toDateString(),
            'tanggal_selesai' => optional($tanggalSelesai)?->toDateString(),
            'status' => (string) ($jadwal->status_diklat ?? ''),
            'tempat' => (string) ($diklat?->tempat ?? ''),
            'waktu' => optional($diklat?->waktu)?->format('H:i:s'),
            'jp' => $diklat?->jp,
            'total_biaya' => $diklat?->total_biaya,
            'jenis_biaya' => (string) ($diklat?->jenisBiaya?->nama ?? ''),
            'jenis_pelaksana' => (string) ($diklat?->jenis_pelaksanaan ?? ''),
            'catatan' => (string) ($diklat?->catatan ?? ''),
            'pegawai_id' => (int) ($jadwal->pegawai?->id ?? 0),
            'pegawai_nama' => (string) ($jadwal->pegawai?->nama ?? ''),
            'pegawai_nik' => (string) ($jadwal->pegawai?->nik ?? ''),
            'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
            'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
            'status_kelayakan' => $jadwal->status_kelayakan,
            'status_validasi' => $jadwal->status_validasi,
        ];
    }

    private function hasMissingLaporan(?string $sertifFilePath, ?string $noSertif): bool
    {
        return trim((string) $sertifFilePath) === '' || trim((string) $noSertif) === '';
    }

    private function sendNotifDiklatWa(int $pegawaiId, string $namaDiklat, string $statusPesan): void
    {
        if ($pegawaiId <= 0) {
            return;
        }

        $pegawai = Pegawai::with('pribadi')->find($pegawaiId);
        $noTelp = $pegawai?->pribadi?->no_telp ?? '';
        if ($noTelp === '') {
            return;
        }

        $no = preg_replace('/\D/', '', $noTelp);
        if (str_starts_with($no, '0')) {
            $no = '62'.substr($no, 1);
        } elseif (! str_starts_with($no, '62')) {
            $no = '62'.$no;
        }

        $pesan = "Halo {$pegawai->nama},\n\n{$statusPesan}\n\nSilakan login ke aplikasi SIMPEG untuk informasi lebih lanjut.";

        $this->whatsapp->sendMessage($no, $pesan);
    }
}
