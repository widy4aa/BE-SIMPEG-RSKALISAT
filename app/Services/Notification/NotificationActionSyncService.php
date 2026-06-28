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
        $pegawai = \App\Models\Pegawai::where('user_id', $userId)
            ->with(['str' => function ($q) {
                $q->orderBy('id', 'desc');
            }, 'pribadi.pasangan', 'pribadi.anak', 'pribadi.orangTua', 'pribadi.kontakDarurat'])
            ->first();

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

        $pribadi = $pegawai->pribadi;
        $profileKeterangan = [];
        if (blank($pegawai->nik) && blank($pegawai->nip)) $profileKeterangan[] = 'NIK / NIP belum terisi';
        if (blank($pegawai->jenis_pegawai_id)) $profileKeterangan[] = 'Jenis pegawai belum terisi';
        if (blank($pegawai->profesi_id)) $profileKeterangan[] = 'Profesi belum terisi';
        if (blank($pegawai->tgl_masuk)) $profileKeterangan[] = 'Tanggal masuk belum terisi';
        if (!$pribadi) {
            $profileKeterangan[] = 'Data pribadi belum terisi';
        } else {
            if (blank($pribadi->tanggal_lahir)) $profileKeterangan[] = 'Tanggal lahir belum terisi';
            if (blank($pribadi->jenis_kelamin)) $profileKeterangan[] = 'Jenis kelamin belum terisi';
            if (blank($pribadi->agama)) $profileKeterangan[] = 'Agama belum terisi';
            if (blank($pribadi->alamat)) $profileKeterangan[] = 'Alamat belum terisi';
            if (blank($pribadi->no_telp)) $profileKeterangan[] = 'Nomor telepon belum terisi';
            if (blank($pribadi->pendidikan_terakhir)) $profileKeterangan[] = 'Pendidikan terakhir belum terisi';
            if (blank($pribadi->ktp_file_path)) $profileKeterangan[] = 'Dokumen KTP belum diunggah';
            if (blank($pribadi->kk_file_path)) $profileKeterangan[] = 'Dokumen KK belum diunggah';
        }

        if (!empty($profileKeterangan)) {
            $activeUniqueKeys[] = 'dashboard.profile.incomplete';
            $this->notificationRepository->upsertAction(
                userId: $userId,
                uniqueKey: 'dashboard.profile.incomplete',
                actionCode: 'profile_incomplete',
                title: 'Data profil belum lengkap',
                message: 'Silakan lengkapi data profil pribadi dan dokumen Anda.',
                payload: [
                    'status_lengkap' => false,
                    'keterangan' => $profileKeterangan,
                ],
            );
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
