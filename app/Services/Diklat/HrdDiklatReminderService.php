<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use App\Repositories\Notification\NotificationRepository;
use App\Services\Notification\WhatsappService;
use InvalidArgumentException;

class HrdDiklatReminderService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
        private readonly NotificationRepository $notificationRepository,
        private readonly WhatsappService $whatsapp,
    ) {}

    public function remindUploadLaporan(int $diklatId, int $pegawaiId): array
    {
        if ($diklatId <= 0) {
            throw new InvalidArgumentException('ID diklat tidak valid.');
        }

        if ($pegawaiId <= 0) {
            throw new InvalidArgumentException('ID pegawai tidak valid.');
        }

        $jadwal = $this->pegawaiDiklatRepository->findJadwalReminderUploadLaporan($diklatId, $pegawaiId);
        if ($jadwal === null) {
            throw new InvalidArgumentException('Data peserta diklat tidak ditemukan.');
        }

        $labelDokumen = $this->labelDokumen($jadwal->jenis_pelaksanaan ?? null);
        if (trim((string) ($jadwal->sertif_file_path ?? '')) !== '') {
            throw new InvalidArgumentException("Pegawai sudah upload {$labelDokumen} diklat.");
        }

        $namaDiklat = (string) ($jadwal->nama_kegiatan ?? 'Diklat');
        $tanggalSelesai = optional($jadwal->tanggal_selesai)->toDateString() ?? '-';
        $title = 'Segera Upload '.ucfirst($labelDokumen).' Diklat';
        $message = "Diklat '{$namaDiklat}' telah selesai ({$tanggalSelesai}). Segera upload {$labelDokumen} Anda melalui menu Diklat.";

        $this->notificationRepository->createInfo((int) $jadwal->user_id, $title, $message);

        $whatsappSent = false;
        $whatsappMessage = null;
        $noTelp = trim((string) ($jadwal->no_telp ?? ''));

        if ($noTelp !== '') {
            $templateKey = 'wa_template_diklat_laporan';
            $templateDefault = "📋 Halo {nama},\n\nDiklat *{nama_diklat}* telah selesai kemarin ({tanggal_selesai}).\n\nSegera upload *{label_dokumen}* Anda melalui aplikasi SIMPEG agar dapat diproses oleh HRD. 🙏";
            $template = \App\Models\Setting::where('key', $templateKey)->value('value') ?: $templateDefault;

            $pesan = str_replace(
                ['{nama}', '{nama_diklat}', '{tanggal_selesai}', '{label_dokumen}'],
                [$jadwal->pegawai_nama, $namaDiklat, $tanggalSelesai, $labelDokumen],
                $template
            );
            $result = $this->whatsapp->sendMessage($this->formatPhoneNumber($noTelp), $pesan);
            $whatsappSent = (bool) ($result['success'] ?? false);
            $whatsappMessage = $result['message'] ?? null;
        }

        return [
            'id_jadwal_diklat' => (int) $jadwal->id,
            'diklat_id' => (int) $jadwal->diklat_id,
            'pegawai_id' => (int) $jadwal->pegawai_id,
            'pegawai_nama' => (string) ($jadwal->pegawai_nama ?? ''),
            'nama_diklat' => $namaDiklat,
            'jenis_pelaksanaan' => (string) ($jadwal->jenis_pelaksanaan ?? ''),
            'label_dokumen' => $labelDokumen,
            'in_app_sent' => true,
            'whatsapp_sent' => $whatsappSent,
            'whatsapp_message' => $whatsappMessage,
        ];
    }

    private function labelDokumen(?string $jenisPelaksanaan): string
    {
        return strtolower((string) $jenisPelaksanaan) === 'internal' ? 'laporan' : 'sertifikat';
    }

    private function formatPhoneNumber(string $no): string
    {
        $no = preg_replace('/\D/', '', $no);
        if (str_starts_with($no, '0')) {
            return '62'.substr($no, 1);
        }

        if (str_starts_with($no, '62')) {
            return $no;
        }

        return '62'.$no;
    }
}
