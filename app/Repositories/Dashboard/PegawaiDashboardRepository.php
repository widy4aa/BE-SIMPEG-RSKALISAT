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
                'strs' => fn ($query) => $query->orderByDesc('tanggal_kadaluarsa'),
                'pribadi.keluarga',
                'jabatan',
                'jenisPegawai',
                'unitKerjaPegawai.unitKerja',
            ])
            ->where('user_id', $userId)
            ->first();
    }

    public function getUnreadNotificationsByUserId(int $userId): Collection
    {
        return NotificationModel::query()
            ->where('user_id', $userId)
            ->where('is_read', false)
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
