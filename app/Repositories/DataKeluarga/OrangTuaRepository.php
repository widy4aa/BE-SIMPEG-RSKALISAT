<?php

namespace App\Repositories\DataKeluarga;

use App\Models\OrangTua;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;

class OrangTuaRepository
{
    public function getOrangTuaByUserId(int $userId)
    {
        $pegawai = Pegawai::where('user_id', $userId)->first();
        if (!$pegawai) return collect();

        $pribadi = $pegawai->pribadi;
        if (!$pribadi) return collect();

        return $pribadi->orangTua;
    }

    public function findPegawaiByUserIdWithPribadi(int $userId): ?Pegawai
    {
        return Pegawai::query()->with('pribadi')->where('user_id', $userId)->first();
    }

    public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function findByIdAndUserId(int $id, int $userId): ?OrangTua
    {
        return OrangTua::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi.pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function create(array $data): OrangTua
    {
        return OrangTua::query()->create($data);
    }

    public function update(OrangTua $orangTua, array $data): bool
    {
        return $orangTua->update($data);
    }

    public function delete(OrangTua $orangTua): bool
    {
        return $orangTua->delete();
    }
}
