<?php

namespace App\Repositories\RiwayatKarir\Managed;

use App\Models\Pegawai;
use App\Models\StrPegawai;
use Illuminate\Support\Collection;

class StrRepository
{
    public function findPegawaiById(int $pegawaiId): ?Pegawai
    {
        return Pegawai::query()->where('id', $pegawaiId)->first();
    }

    public function getStrByPegawaiId(int $pegawaiId): Collection
    {
        return StrPegawai::query()->where('pegawai_id', $pegawaiId)->orderByDesc('id')->get();
    }

    public function findStrByIdAndPegawaiId(int $id, int $pegawaiId): ?StrPegawai
    {
        return StrPegawai::query()->where('id', $id)->where('pegawai_id', $pegawaiId)->first();
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
