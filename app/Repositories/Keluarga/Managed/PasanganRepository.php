<?php

namespace App\Repositories\Keluarga\Managed;

use App\Models\Pasangan;
use App\Models\PegawaiPribadi;
use Illuminate\Support\Collection;

class PasanganRepository
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

    public function getPasanganByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return Pasangan::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findPasanganByIdAndPegawaiId(int $id, int $pegawaiId): ?Pasangan
    {
        return Pasangan::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createPasangan(array $data): Pasangan
    {
        return Pasangan::query()->create($data);
    }

    public function updatePasangan(Pasangan $pasangan, array $data): Pasangan
    {
        $pasangan->update($data);
        return $pasangan->fresh();
    }

    public function deletePasangan(Pasangan $pasangan): void
    {
        $pasangan->delete();
    }
}
