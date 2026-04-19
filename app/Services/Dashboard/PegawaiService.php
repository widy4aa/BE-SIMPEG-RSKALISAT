<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\PegawaiDashboardRepository;
use App\Services\Notification\NotificationActionSyncService;

class PegawaiService
{
    public function __construct(
        private readonly PegawaiDashboardRepository $pegawaiDashboardRepository,
        private readonly NotificationActionSyncService $notificationActionSyncService,
    ) {
    }

    public function build(int $userId): array
    {
        // Sinkronisasi ringan aksi dashboard agar list_aksi berasal dari notifikasi action.
        $this->notificationActionSyncService->syncDashboardActionsByUserId($userId);

        $pegawai = $this->pegawaiDashboardRepository->findPegawaiDashboardByUserId($userId);

        $unitKerjaAktif = $pegawai?->unitKerjaPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->unitKerjaPegawai?->first();

        $listNotifikasi = $this->pegawaiDashboardRepository
            ->getUnreadInfoNotificationsByUserId($userId)
            ->map(function ($notification) {
                return [
                    'id' => (int) $notification->id,
                    'title' => (string) ($notification->title ?? ''),
                    'message' => (string) ($notification->message ?? ''),
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => optional($notification->created_at)?->toDateTimeString(),
                ];
            })
            ->values()
            ->all();
        $listAksi = $this->pegawaiDashboardRepository
            ->getActiveActionNotificationsByUserId($userId)
            ->map(function ($notification) {
                return [
                    'id' => (int) $notification->id,
                    'action_code' => (string) ($notification->action_code ?? ''),
                    'title' => (string) ($notification->title ?? ''),
                    'message' => (string) ($notification->message ?? ''),
                    'action_payload' => (array) ($notification->action_payload ?? []),
                    'is_read' => (bool) $notification->is_read,
                    'is_resolved' => (bool) $notification->is_resolved,
                    'created_at' => optional($notification->created_at)?->toDateTimeString(),
                ];
            })
            ->values()
            ->all();
        $listJadwalDiklatMendatang = $this->pegawaiDashboardRepository
            ->getUpcomingDiklatByPegawaiId((int) ($pegawai?->id ?? 0))
            ->map(function ($jadwal) {
                return [
                    'jadwal_id' => (int) $jadwal->id,
                    'status_diklat' => (string) $jadwal->status_diklat,
                    'nama_kegiatan' => (string) ($jadwal->diklat?->nama_kegiatan ?? ''),
                    'penyelenggara' => (string) ($jadwal->diklat?->penyelenggara ?? ''),
                    'tanggal_mulai' => optional($jadwal->diklat?->tanggal_mulai)?->toDateString(),
                    'tanggal_selesai' => optional($jadwal->diklat?->tanggal_selesai)?->toDateString(),
                    'tempat' => (string) ($jadwal->diklat?->tempat ?? ''),
                    'waktu' => optional($jadwal->diklat?->waktu)?->format('H:i:s'),
                ];
            })
            ->values()
            ->all() ?? [];

        return [
            'welcome' => 'Selamat datang pegawai',
            'summary' => [
                'label' => 'Dashboard pegawai',
                'nama' => (string) ($pegawai?->nama ?? ''),
                'nip' => (string) ($pegawai?->nip ?? ''),
                'jabatan' => (string) ($pegawai?->jabatan?->nama ?? ''),
                'jenis_jabatan' => (string) ($pegawai?->jenisPegawai?->nama ?? ''),
                'unit_kerja' => (string) ($unitKerjaAktif?->unitKerja?->nama ?? ''),
                'jumlah_diklat_selesai' => (int) ($pegawai?->jumlah_diklat_selesai ?? 0),
                'jumlah_diklat_dijadwalkan_belum_selesai' => (int) ($pegawai?->jumlah_diklat_belum_selesai ?? 0),
                'list_jadwal_diklat_mendatang' => $listJadwalDiklatMendatang,
                'list_notifikasi' => $listNotifikasi,
                'list_aksi' => $listAksi,
            ],
        ];
    }
}
