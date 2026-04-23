<?php

namespace App\Repositories\DataKeluarga;

use App\Models\Pegawai;

class DataKeluargaRepository
{
    public function getKeluargaByUserId(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with([
                'pribadi.pasangan',
                'pribadi.anak',
                'pribadi.orangTua',
                'pribadi.kontakDarurat',
                'pribadi.tanggunganLain',
            ])
            ->where('user_id', $userId)
            ->first();
    }
}
