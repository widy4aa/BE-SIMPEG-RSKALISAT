<?php

namespace App\Repositories\Keluarga\Managed;

use App\Models\OrangTua;
use App\Models\PegawaiPribadi;
use Illuminate\Support\Collection;

class OrangTuaRepository
{
    public function findPribadiByPegawaiId(int $pegawaiId): ?PegawaiPribadi
    {
        return PegawaiPribadi::query()->where('pegawai_id', $pegawaiId)->first();
    }

    public function createPribadiIfNotExists(int $pegawaiId): PegawaiPribadi
    {
        return $this->findPribadiByPegawaiId($pegawaiId)
            ?? PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function getOrangTuaByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return OrangTua::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findOrangTuaByIdAndPegawaiId(int $id, int $pegawaiId): ?OrangTua
    {
        return OrangTua::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createOrangTua(array $data): OrangTua
    {
        return OrangTua::query()->create($data);
    }

    public function updateOrangTua(OrangTua $orangTua, array $data): OrangTua
    {
        $orangTua->update($data);
        return $orangTua->fresh();
    }

    public function deleteOrangTua(OrangTua $orangTua): void
    {
        $orangTua->delete();
    }
}
