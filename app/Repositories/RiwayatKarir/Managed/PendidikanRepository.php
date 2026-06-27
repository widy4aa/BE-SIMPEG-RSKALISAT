<?php

namespace App\Repositories\RiwayatKarir\Managed;

use App\Models\Pendidikan;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use Illuminate\Support\Collection;

class PendidikanRepository
{
    public function findPegawaiById(int $pegawaiId): ?Pegawai
    {
        return Pegawai::query()->where('id', $pegawaiId)->first();
    }

    public function findPribadiByPegawaiId(int $pegawaiId): ?PegawaiPribadi
    {
        return PegawaiPribadi::query()->where('pegawai_id', $pegawaiId)->first();
    }

    public function createPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function getPendidikanByPribadiId(int $pribadiId): Collection
    {
        return Pendidikan::query()
            ->where('pegawai_pribadi_id', $pribadiId)
            ->orderByDesc('tahun_lulus')
            ->get();
    }

    public function findPendidikanByIdAndPribadiId(int $id, int $pribadiId): ?Pendidikan
    {
        return Pendidikan::query()
            ->where('id', $id)
            ->where('pegawai_pribadi_id', $pribadiId)
            ->first();
    }

    public function createPendidikan(int $pribadiId, array $data): Pendidikan
    {
        $data['pegawai_pribadi_id'] = $pribadiId;
        return Pendidikan::query()->create($data);
    }

    public function updatePendidikan(Pendidikan $pendidikan, array $data): Pendidikan
    {
        $pendidikan->update($data);
        return $pendidikan->fresh();
    }

    public function deletePendidikan(Pendidikan $pendidikan): void
    {
        $pendidikan->delete();
    }
}
