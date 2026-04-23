<?php

namespace App\Services\Notification;

use App\Repositories\Dashboard\PegawaiDashboardRepository;
use App\Repositories\Notification\NotificationRepository;

class NotificationActionSyncService
{
    public function __construct(
        private readonly PegawaiDashboardRepository $pegawaiDashboardRepository,
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    public function syncDashboardActionsByUserId(int $userId): void
    {
        $pegawai = $this->pegawaiDashboardRepository->findPegawaiDashboardByUserId($userId);

        if ($pegawai === null) {
            return;
        }

        $activeUniqueKeys = [];

        $strTerbaru = $pegawai->str->first();

        if ($strTerbaru === null || $strTerbaru->tanggal_kadaluarsa === null) {
            $activeUniqueKeys[] = 'dashboard.str.missing';
            $this->notificationRepository->upsertAction(
                userId: $userId,
                uniqueKey: 'dashboard.str.missing',
                actionCode: 'str_missing',
                title: 'STR belum tersedia',
                message: 'Silakan lengkapi data STR Anda.',
                payload: [
                    'status_lengkap' => false,
                    'sisa_hari' => null,
                    'keterangan' => ['STR belum tersedia'],
                ],
            );
        } else {
            $sisaHari = now()->startOfDay()->diffInDays($strTerbaru->tanggal_kadaluarsa->startOfDay(), false);

            if ($sisaHari < 0) {
                $activeUniqueKeys[] = 'dashboard.str.expired';
                $this->notificationRepository->upsertAction(
                    userId: $userId,
                    uniqueKey: 'dashboard.str.expired',
                    actionCode: 'str_expired',
                    title: 'STR sudah kadaluarsa',
                    message: 'Segera perbarui STR karena sudah melewati masa berlaku.',
                    payload: [
                        'status_lengkap' => true,
                        'sisa_hari' => $sisaHari,
                        'keterangan' => ['STR sudah kadaluarsa'],
                    ],
                );
            } elseif ($sisaHari <= 90) {
                $activeUniqueKeys[] = 'dashboard.str.will_expire';
                $this->notificationRepository->upsertAction(
                    userId: $userId,
                    uniqueKey: 'dashboard.str.will_expire',
                    actionCode: 'str_will_expire',
                    title: 'STR akan segera kadaluarsa',
                    message: 'STR Anda akan kadaluarsa dalam waktu dekat. Segera lakukan perpanjangan.',
                    payload: [
                        'status_lengkap' => true,
                        'sisa_hari' => $sisaHari,
                        'keterangan' => ['STR aktif'],
                    ],
                );
            }
        }

        $pasangan = $pegawai->pribadi?->pasangan ?? collect();
        $anak = $pegawai->pribadi?->anak ?? collect();
        $orangTua = $pegawai->pribadi?->orangTua ?? collect();
        $kontakDarurat = $pegawai->pribadi?->kontakDarurat ?? collect();

        $bukuNikahFilePath = (string) ($pegawai->pribadi?->buku_nikah_file_path ?? '');
        $keterangan = [];

        if ($bukuNikahFilePath === '') {
            $keterangan[] = 'bukti pernikahan belum ada';
        }

        if ($pasangan->isEmpty() && $anak->isEmpty() && $orangTua->isEmpty() && $kontakDarurat->isEmpty()) {
            $keterangan[] = 'data keluarga belum ada';
        }

        foreach ($pasangan as $p) {
            if (blank($p->nama_lengkap) || blank($p->tanggal_lahir)) {
                $keterangan[] = (string) ($p->nama_lengkap ?: 'nama pasangan kosong');
            }
        }
        
        foreach ($anak as $a) {
            if (blank($a->nama_lengkap) || blank($a->tanggal_lahir)) {
                $keterangan[] = (string) ($a->nama_lengkap ?: 'nama anak kosong');
            }
        }

        $keterangan = array_values(array_unique($keterangan));

        if (! empty($keterangan)) {
            $activeUniqueKeys[] = 'dashboard.keluarga.incomplete';
            $this->notificationRepository->upsertAction(
                userId: $userId,
                uniqueKey: 'dashboard.keluarga.incomplete',
                actionCode: 'keluarga_incomplete',
                title: 'Data keluarga belum lengkap',
                message: 'Silakan lengkapi data keluarga Anda.',
                payload: [
                    'status_lengkap' => false,
                    'keterangan' => $keterangan,
                ],
            );
        }

        $this->notificationRepository->resolveActionsNotIn($userId, $activeUniqueKeys);
    }
}
