<?php

namespace App\Repositories\Pegawai\Managed;

use App\Models\Pegawai;
use App\Models\PegawaiPribadi;

class PegawaiRepository
{
    public function findPegawaiById(int $id): ?Pegawai
    {
        return Pegawai::query()->where('id', $id)->first();
    }

    public function findPribadiByPegawaiId(int $pegawaiId): ?PegawaiPribadi
    {
        return PegawaiPribadi::query()->where('pegawai_id', $pegawaiId)->first();
    }

    public function createPribadiIfNotExists(int $pegawaiId): PegawaiPribadi
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);

        return $pribadi ?? PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function updatePegawaiInti(Pegawai $pegawai, array $data): Pegawai
    {
        $pegawai->update($data);
        return $pegawai->fresh();
    }

    public function updatePegawaiPribadi(PegawaiPribadi $pribadi, array $data): PegawaiPribadi
    {
        $pribadi->update($data);
        return $pribadi->fresh();
    }
}
