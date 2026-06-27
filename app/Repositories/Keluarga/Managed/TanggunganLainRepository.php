<?php

namespace App\Repositories\Keluarga\Managed;

use App\Models\PegawaiPribadi;
use App\Models\TanggunganLain;
use Illuminate\Support\Collection;

class TanggunganLainRepository
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

    public function getTanggunganLainByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return TanggunganLain::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findTanggunganLainByIdAndPegawaiId(int $id, int $pegawaiId): ?TanggunganLain
    {
        return TanggunganLain::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createTanggunganLain(array $data): TanggunganLain
    {
        return TanggunganLain::query()->create($data);
    }

    public function updateTanggunganLain(TanggunganLain $tanggungan, array $data): TanggunganLain
    {
        $tanggungan->update($data);
        return $tanggungan->fresh();
    }

    public function deleteTanggunganLain(TanggunganLain $tanggungan): void
    {
        $tanggungan->delete();
    }
}
