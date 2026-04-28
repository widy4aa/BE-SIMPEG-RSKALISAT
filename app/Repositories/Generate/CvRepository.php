<?php

namespace App\Repositories\Generate;

use App\Models\Pegawai;

class CvRepository
{
    public function getPegawaiForCv(int $pegawaiId): ?Pegawai
    {
        return Pegawai::with([
            'user',
            'pribadi.pendidikan',
            'jenisPegawai',
            'profesi',
            'jabatan.unitKerja',
            'pangkat',
            'golonganRuang',
            'jadwalDiklat.diklat.kategoriDiklat'
        ])->find($pegawaiId);
    }

    public function getPegawaiIdByUserId(int $userId): ?int
    {
        return Pegawai::where('user_id', $userId)->value('id');
    }
}
