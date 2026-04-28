<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Collection;

class AdminPegawaiRepository
{
    public function getAllPegawai(): Collection
    {
        return Pegawai::with([
            'user',
            'pribadi',
            'profesi',
            'jabatan.unitKerja'
        ])->get();
    }
}
