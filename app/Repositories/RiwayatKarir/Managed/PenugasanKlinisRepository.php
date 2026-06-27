<?php

namespace App\Repositories\RiwayatKarir\Managed;

use App\Models\Pegawai;
use App\Models\PenugasanKlinis;
use Illuminate\Support\Collection;

class PenugasanKlinisRepository
{
    public function findPegawaiById(int $pegawaiId): ?Pegawai
    {
        return Pegawai::query()->where('id', $pegawaiId)->first();
    }

    public function getPenugasanKlinisByPegawaiId(int $pegawaiId): Collection
    {
        return PenugasanKlinis::query()->where('pegawai_id', $pegawaiId)->orderByDesc('id')->get();
    }

    public function findPenugasanKlinisByIdAndPegawaiId(int $id, int $pegawaiId): ?PenugasanKlinis
    {
        return PenugasanKlinis::query()->where('id', $id)->where('pegawai_id', $pegawaiId)->first();
    }

    public function createPenugasanKlinis(Pegawai $pegawai, array $data): PenugasanKlinis
    {
        $pk = new PenugasanKlinis($data);
        $pegawai->penugasanKlinis()->save($pk);
        return $pk;
    }

    public function updatePenugasanKlinis(PenugasanKlinis $pk, array $data): PenugasanKlinis
    {
        $pk->update($data);
        return $pk->fresh();
    }

    public function deletePenugasanKlinis(PenugasanKlinis $pk): void
    {
        $pk->delete();
    }
}
