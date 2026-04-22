<?php

namespace App\Repositories\RiwayatKarir;

use App\Models\PegawaiPribadi;
use App\Models\Pendidikan;
use App\Models\Pegawai;

class PendidikanRepository
{
    public function findPegawaiByUserIdWithPendidikan(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with(['pribadi.pendidikan'])
            ->where('user_id', $userId)
            ->first();
    }

    public function findPegawaiByUserIdWithPribadi(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with('pribadi')
            ->where('user_id', $userId)
            ->first();
    }

    public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawaiId,
        ]);
    }

    public function createPendidikan(array $attributes): Pendidikan
    {
        return Pendidikan::query()->create($attributes);
    }

    public function findByIdAndUserId(int $id, int $userId): ?Pendidikan
    {
        return Pendidikan::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi.pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function updatePendidikan(Pendidikan $pendidikan, array $attributes): Pendidikan
    {
        $pendidikan->update($attributes);
        return $pendidikan;
    }

    public function deletePendidikan(Pendidikan $pendidikan): bool
    {
        return $pendidikan->delete();
    }
}
