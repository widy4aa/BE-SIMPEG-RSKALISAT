<?php

namespace App\Repositories\Dashboard;

use App\Models\NotificationModel;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class PegawaiDashboardRepository
{
    public function findPegawaiDashboardByUserId(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->withCount([
                'jadwalDiklat as jumlah_diklat_selesai' => fn ($query) => $query->where('status_diklat', 'sudah terlaksana'),
                'jadwalDiklat as jumlah_diklat_belum_selesai' => fn ($query) => $query->whereIn('status_diklat', ['belum terlaksana', 'sedang terlaksana']),
            ])
            ->with([
                'str' => fn ($query) => $query->orderByDesc('tanggal_kadaluarsa'),
                'pribadi.pasangan',
                'pribadi.anak',
                'pribadi.orangTua',
                'pribadi.kontakDarurat',
                'pribadi.tanggunganLain',
                'jabatan',
                'jenisPegawai',
                'unitKerjaPegawai.unitKerja',
            ])
            ->where('user_id', $userId)
            ->first();
    }

    public function getUnreadInfoNotificationsByUserId(int $userId): Collection
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('type', 'info')
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getActiveActionNotificationsByUserId(int $userId): Collection
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('type', 'action')
            ->where('is_resolved', false)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getUpcomingDiklatByPegawaiId(int $pegawaiId): Collection
    {
        $pegawai = Pegawai::query()->find($pegawaiId);

        if ($pegawai === null) {
            return collect();
        }

        return $pegawai->jadwalDiklat()
            ->with('diklat')
            ->where('status_diklat', 'belum terlaksana')
            ->get();
    }
}
