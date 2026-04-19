<?php

namespace App\Repositories\Diklat;

use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class PegawaiDiklatRepository
{
    public function findPegawaiByUserId(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->where('user_id', $userId)
            ->first();
    }

    public function getRiwayatDiklatByPegawaiId(int $pegawaiId): Collection
    {
        return ListJadwalDiklat::query()
            ->with([
                'diklat.kategoriDiklat',
                'diklat.jenisDiklat',
                'diklat.jenisBiaya',
                'diklat.createdByPegawai',
            ])
            ->where('pegawai_id', $pegawaiId)
            ->orderByDesc('id')
            ->get();
    }
}
