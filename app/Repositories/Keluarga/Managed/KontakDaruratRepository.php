<?php

namespace App\Repositories\Keluarga\Managed;

use App\Models\KontakDarurat;
use App\Models\PegawaiPribadi;
use Illuminate\Support\Collection;

class KontakDaruratRepository
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

    public function getKontakDaruratByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return KontakDarurat::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findKontakDaruratByIdAndPegawaiId(int $id, int $pegawaiId): ?KontakDarurat
    {
        return KontakDarurat::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createKontakDarurat(array $data): KontakDarurat
    {
        return KontakDarurat::query()->create($data);
    }

    public function updateKontakDarurat(KontakDarurat $kontakDarurat, array $data): KontakDarurat
    {
        $kontakDarurat->update($data);
        return $kontakDarurat->fresh();
    }

    public function deleteKontakDarurat(KontakDarurat $kontakDarurat): void
    {
        $kontakDarurat->delete();
    }
}
