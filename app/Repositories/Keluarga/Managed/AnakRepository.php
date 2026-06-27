<?php

namespace App\Repositories\Keluarga\Managed;

use App\Models\Anak;
use App\Models\PegawaiPribadi;
use Illuminate\Support\Collection;

class AnakRepository
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

    public function getAnakByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return Anak::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findAnakByIdAndPegawaiId(int $id, int $pegawaiId): ?Anak
    {
        return Anak::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createAnak(array $data): Anak
    {
        return Anak::query()->create($data);
    }

    public function updateAnak(Anak $anak, array $data): Anak
    {
        $anak->update($data);
        return $anak->fresh();
    }

    public function deleteAnak(Anak $anak): void
    {
        $anak->delete();
    }
}
