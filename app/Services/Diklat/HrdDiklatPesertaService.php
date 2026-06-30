<?php

namespace App\Services\Diklat;

use App\Models\Pegawai;
use App\Repositories\Diklat\PegawaiDiklatRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Services\Notification\WhatsappService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HrdDiklatPesertaService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
        private readonly DiklatStatusResolver $statusResolver,
        private readonly NotificationRepository $notificationRepository,
        private readonly WhatsappService $whatsapp,
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

    private function sendNotifTerdaftarDiklat(int $pegawaiId, string $namaDiklat): void
    {
        $pegawai = Pegawai::with('pribadi')->find($pegawaiId);
        if ($pegawai === null) {
            return;
        }

        // Notifikasi in-app
        $userIdPegawai = (int) ($pegawai->user_id ?? 0);
        if ($userIdPegawai > 0) {
            $this->notificationRepository->createInfo(
                $userIdPegawai,
                'Terdaftar ke Diklat Baru',
                "Anda telah didaftarkan ke diklat '{$namaDiklat}'. Silakan cek jadwal di aplikasi."
            );
        }

        // Notifikasi WhatsApp
        $noTelp = (string) ($pegawai->pribadi?->no_telp ?? '');
        if ($noTelp !== '') {
            $no = preg_replace('/\D/', '', $noTelp);
            if (str_starts_with($no, '0')) {
                $no = '62' . substr($no, 1);
            } elseif (! str_starts_with($no, '62')) {
                $no = '62' . $no;
            }
            $pesan = "🎓 Halo {$pegawai->nama},\n\nAnda telah *didaftarkan* ke diklat:\n*{$namaDiklat}*\n\nSilakan login ke aplikasi SIMPEG untuk melihat detail jadwal.";
            $this->whatsapp->sendMessage($no, $pesan);
        }
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
                    'diklat_id'       => $diklatId,
                    'pegawai_id'      => $pegawaiId,
                    'status_diklat'   => $statusDiklat,
                    'status_kelayakan'=> 'layak',
                    'status_validasi' => null,
                ]);

                $this->sendNotifTerdaftarDiklat((int) $pegawaiId, (string) ($diklat->nama_kegiatan ?? 'Diklat'));
            }
        });

        return [
            'diklat_id' => $diklatId,
            'peserta_terdaftar' => count($pegawaiIds),
        ];
    }

    public function remindLaporanDiklat(int $diklatId, int $pegawaiId): array
    {
        $diklat = $this->pegawaiDiklatRepository->findDiklatById($diklatId);
        if ($diklat === null) {
            throw new InvalidArgumentException('Master Diklat tidak ditemukan.');
        }

        $jadwal = $this->pegawaiDiklatRepository->findJadwalByDiklatIdAndPegawaiId($diklatId, $pegawaiId);
        if ($jadwal === null) {
            throw new InvalidArgumentException('Pegawai tidak terdaftar pada diklat ini.');
        }

        $jenis        = strtolower((string) ($diklat->jenis_pelaksanaan ?? ''));
        $labelDokumen = $jenis === 'internal' ? 'laporan' : 'sertifikat';
        $namaDiklat   = (string) ($diklat->nama_kegiatan ?? 'Diklat');

        $pegawai = Pegawai::with('pribadi')->find($pegawaiId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        $userId = (int) ($pegawai->user_id ?? 0);
        if ($userId > 0) {
            $this->notificationRepository->createInfo(
                $userId,
                'Segera Upload ' . ucfirst($labelDokumen) . ' Diklat',
                "Segera upload {$labelDokumen} diklat '{$namaDiklat}' Anda melalui menu Diklat."
            );
        }

        $noTelp = (string) ($pegawai->pribadi?->no_telp ?? '');
        if ($noTelp !== '') {
            $no = preg_replace('/\D/', '', $noTelp);
            if (str_starts_with($no, '0')) {
                $no = '62' . substr($no, 1);
            } elseif (! str_starts_with($no, '62')) {
                $no = '62' . $no;
            }
            $pesan = "📋 Halo {$pegawai->nama},\n\nHRD mengingatkan Anda untuk segera upload *{$labelDokumen}* diklat:\n*{$namaDiklat}*\n\nSilakan login ke aplikasi SIMPEG dan upload melalui menu Diklat. 🙏";
            $this->whatsapp->sendMessage($no, $pesan);
        }

        return [
            'diklat_id'  => $diklatId,
            'pegawai_id' => $pegawaiId,
            'nama_diklat' => $namaDiklat,
            'label_dokumen' => $labelDokumen,
            'notif_inapp' => $userId > 0,
            'notif_wa'    => $noTelp !== '',
        ];
    }

}
