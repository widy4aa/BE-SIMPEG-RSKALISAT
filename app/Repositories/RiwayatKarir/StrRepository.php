<?php

namespace App\Repositories\RiwayatKarir;

use App\Models\Pegawai;
use App\Models\StrPegawai;

class StrRepository
{
    public function findPegawaiByUserIdWithStr(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with(['str'])
            ->where('user_id', $userId)
            ->first();
    }

    public function findStrByIdAndUserId(int $id, int $userId): ?StrPegawai
    {
        return StrPegawai::query()
            ->where('id', $id)
            ->whereHas('pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function createStr(Pegawai $pegawai, array $data): StrPegawai
    {
        $str = new StrPegawai($data);
        $pegawai->str()->save($str);
        return $str;
    }

    public function updateStr(StrPegawai $str, array $data): StrPegawai
    {
        $str->update($data);
        return $str->fresh();
    }

    public function deleteStr(StrPegawai $str): void
    {
        $str->delete();
    }
}
