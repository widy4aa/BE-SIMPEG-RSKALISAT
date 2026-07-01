<?php

namespace App\Services\Notification;

use App\Repositories\Notification\NotificationRepository;

class NotificationActionSyncService
{
    /**
     * Rentang hari notifikasi masa berlaku aktif:
     * - MAX_BEFORE : mulai notif dari 90 hari sebelum kadaluarsa
     * - MAX_AFTER  : hentikan (resolve) notif setelah 7 hari pasca kadaluarsa
     *
     * Milestone informatif (ditampilkan di payload keterangan):
     * 90, 60, 30, 21, 14, 7, 3, 2, 1 hari sebelum
     * 0 (hari kadaluarsa), 3, 7 hari sesudah
     */
    private const MAX_BEFORE = 90;
    private const MAX_AFTER  = 7;

    public function __construct(
        private readonly NotificationRepository $notificationRepository,
    ) {
    }

    public function syncDashboardActionsByUserId(int $userId): void
    {
        $pegawai = \App\Models\Pegawai::where('user_id', $userId)
            ->with([
                'str' => fn ($q) => $q->orderBy('id', 'desc'),
                'sip' => fn ($q) => $q->orderBy('id', 'desc'),
                'penugasanKlinis' => fn ($q) => $q->orderBy('id', 'desc'),
                'pribadi.pasangan',
                'pribadi.anak',
                'pribadi.orangTua',
                'pribadi.kontakDarurat',
            ])
            ->first();

        if ($pegawai === null) {
            return;
        }

        $activeUniqueKeys = [];

        // === SYNC STR ===
        $strTerbaru = $this->selectMasaBerlakuDocument($pegawai->str, 'tanggal_kadaluarsa');

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
                    'sisa_hari'      => null,
                    'keterangan'     => ['STR belum tersedia'],
                ],
            );
        } else {
            $sisaHari = now()->startOfDay()->diffInDays(
                $strTerbaru->tanggal_kadaluarsa->startOfDay(), false
            );
            $this->syncMasaBerlaku(
                userId: $userId,
                label: 'STR',
                keyPrefix: 'dashboard.str',
                codePrefix: 'str',
                sisaHari: $sisaHari,
                activeUniqueKeys: $activeUniqueKeys,
            );
        }

        // === SYNC SIP ===
        $sipTerbaru = $this->selectMasaBerlakuDocument($pegawai->sip, 'tanggal_kadaluarsa');

        if ($sipTerbaru !== null && $sipTerbaru->tanggal_kadaluarsa !== null) {
            $sisaHari = now()->startOfDay()->diffInDays(
                $sipTerbaru->tanggal_kadaluarsa->startOfDay(), false
            );
            $this->syncMasaBerlaku(
                userId: $userId,
                label: 'SIP',
                keyPrefix: 'dashboard.sip',
                codePrefix: 'sip',
                sisaHari: $sisaHari,
                activeUniqueKeys: $activeUniqueKeys,
            );
        }

        // === SYNC PENUGASAN KLINIS ===
        $pkTerbaru = $this->selectMasaBerlakuDocument($pegawai->penugasanKlinis, 'tgl_kadaluarsa');

        if ($pkTerbaru !== null && $pkTerbaru->tgl_kadaluarsa !== null) {
            $sisaHari = now()->startOfDay()->diffInDays(
                $pkTerbaru->tgl_kadaluarsa->startOfDay(), false
            );
            $this->syncMasaBerlaku(
                userId: $userId,
                label: 'Penugasan Klinis',
                keyPrefix: 'dashboard.penugasan',
                codePrefix: 'penugasan',
                sisaHari: $sisaHari,
                activeUniqueKeys: $activeUniqueKeys,
            );
        }

        // === SYNC PROFIL ===
        $pribadi = $pegawai->pribadi;
        $profileKeterangan = [];
        if (! $pribadi) {
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

        if (! empty($profileKeterangan)) {
            $activeUniqueKeys[] = 'dashboard.profile.incomplete';
            $this->notificationRepository->upsertAction(
                userId: $userId,
                uniqueKey: 'dashboard.profile.incomplete',
                actionCode: 'profile_incomplete',
                title: 'Data profil belum lengkap',
                message: 'Silakan lengkapi data profil pribadi dan dokumen Anda.',
                payload: [
                    'status_lengkap' => false,
                    'keterangan'     => $profileKeterangan,
                ],
            );
        }

        // === SYNC KELUARGA ===
        $pasangan      = $pegawai->pribadi?->pasangan ?? collect();
        $anak          = $pegawai->pribadi?->anak ?? collect();
        $orangTua      = $pegawai->pribadi?->orangTua ?? collect();
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
                    'keterangan'     => $keterangan,
                ],
            );
        }

        $this->notificationRepository->resolveActionsNotIn($userId, $activeUniqueKeys);
    }

    /**
     * Helper: Sinkronisasi notifikasi masa berlaku (STR / SIP / Penugasan Klinis).
     *
     * Aturan aktif/resolve:
     *   sisa_hari > MAX_BEFORE (90)  : belum perlu notif, tidak masuk activeUniqueKeys
     *   0 < sisa_hari <= 90          : "akan kadaluarsa" - will_expire aktif
     *   -MAX_AFTER <= sisa_hari <= 0 : "sudah kadaluarsa" - expired aktif
     *   sisa_hari < -MAX_AFTER (-7)  : di luar rentang, tidak masuk activeUniqueKeys, auto-resolve
     *
     * Milestone informatif di payload (90, 60, 30, 21, 14, 7, 3, 2, 1 hari sebelum;
     * 0 hari kadaluarsa; 3, 7 hari sesudah).
     */
    private function syncMasaBerlaku(
        int $userId,
        string $label,
        string $keyPrefix,
        string $codePrefix,
        int $sisaHari,
        array &$activeUniqueKeys,
    ): void {
        // Terlalu awal, belum perlu notifikasi.
        if ($sisaHari > self::MAX_BEFORE) {
            return;
        }

        // Terlalu lama kadaluarsa, notifikasi di-resolve otomatis.
        if ($sisaHari < -self::MAX_AFTER) {
            return;
        }

        if ($sisaHari <= 0) {
            // Sudah kadaluarsa atau hari ini kadaluarsa.
            $absHari = abs($sisaHari);
            $message = $absHari === 0
                ? "{$label} kadaluarsa hari ini. Segera perbarui."
                : "{$label} sudah kadaluarsa {$absHari} hari yang lalu. Segera perbarui.";

            $keterangan = $absHari === 0
                ? ["{$label} kadaluarsa hari ini"]
                : ["{$label} sudah kadaluarsa sejak {$absHari} hari lalu"];

            $activeUniqueKeys[] = "{$keyPrefix}.expired";
            $this->notificationRepository->upsertAction(
                userId: $userId,
                uniqueKey: "{$keyPrefix}.expired",
                actionCode: "{$codePrefix}_expired",
                title: "{$label} Sudah Kadaluarsa",
                message: $message,
                payload: [
                    'status_lengkap' => true,
                    'sisa_hari'      => $sisaHari,
                    'milestone_label' => $this->resolveMilestoneLabel($sisaHari),
                    'keterangan'     => $keterangan,
                ],
            );
        } else {
            // Akan kadaluarsa (sisa_hari 1-90).
            $message = $sisaHari === 1
                ? "{$label} akan kadaluarsa besok. Segera lakukan perpanjangan."
                : "{$label} akan kadaluarsa dalam {$sisaHari} hari. Segera lakukan perpanjangan.";

            $activeUniqueKeys[] = "{$keyPrefix}.will_expire";
            $this->notificationRepository->upsertAction(
                userId: $userId,
                uniqueKey: "{$keyPrefix}.will_expire",
                actionCode: "{$codePrefix}_will_expire",
                title: "{$label} Akan Segera Kadaluarsa",
                message: $message,
                payload: [
                    'status_lengkap'   => true,
                    'sisa_hari'        => $sisaHari,
                    'milestone_label'  => $this->resolveMilestoneLabel($sisaHari),
                    'keterangan'       => ["{$label} akan kadaluarsa dalam {$sisaHari} hari"],
                ],
            );
        }
    }

    private function selectMasaBerlakuDocument(mixed $documents, string $endField): ?object
    {
        $collection = collect($documents)->filter(fn ($document) => $document->{$endField} !== null);

        if ($collection->isEmpty()) {
            return null;
        }

        $current = $collection
            ->filter(fn ($document) => (bool) ($document->is_current ?? false))
            ->sortByDesc(fn ($document) => optional($document->{$endField})->timestamp ?? 0)
            ->first();

        if ($current !== null) {
            return $current;
        }

        return $collection
            ->sortByDesc(fn ($document) => optional($document->{$endField})->timestamp ?? 0)
            ->first();
    }

    /**
     * Menentukan label milestone berdasarkan sisa_hari.
     * Sebelum kadaluarsa: 90, 60, 30, 21, 14, 7, 3, 2, 1.
     * Hari H: 0. Sesudah kadaluarsa: 3, 7.
     */
    private function resolveMilestoneLabel(int $sisaHari): string
    {
        if ($sisaHari === 0) {
            return 'hari ini';
        }

        if ($sisaHari < 0) {
            $hariLewat = abs($sisaHari);
            $milestonesAfter = [3, 7];

            foreach ($milestonesAfter as $m) {
                if ($hariLewat <= $m) {
                    return "{$m} hari sesudah";
                }
            }

            return "{$hariLewat} hari sesudah";
        }

        $milestonesBefore = [90, 60, 30, 21, 14, 7, 3, 2, 1];

        foreach ($milestonesBefore as $m) {
            if ($sisaHari >= $m) {
                return "{$m} hari sebelum";
            }
        }

        return "{$sisaHari} hari sebelum";
    }
}
