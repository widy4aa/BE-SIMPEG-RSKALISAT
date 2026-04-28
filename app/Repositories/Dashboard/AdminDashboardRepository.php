<?php

namespace App\Repositories\Dashboard;

use App\Models\Pegawai;
use App\Models\PerubahanData;

class AdminDashboardRepository
{
    public function getTotalPegawai(): int
    {
        return Pegawai::query()->count();
    }

    public function getTotalPegawaiAktif(): int
    {
        return Pegawai::query()->where('status_pegawai', 'aktif')->count();
    }

    public function getTotalPermintaanUpdateData(): int
    {
        return PerubahanData::query()->count();
    }

    public function getTotalPermintaanUpdateDataDisetujui(): int
    {
        return PerubahanData::query()->where('status', 'approved')->count();
    }
}
