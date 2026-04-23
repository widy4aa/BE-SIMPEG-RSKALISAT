<?php

namespace App\Repositories\DataKeluarga;

use App\Models\Pasangan;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;

class PasanganRepository
{
    public function getPasanganByUserId(int $userId)
    {
        $pegawai = Pegawai::where('user_id', $userId)->first();
        if (!$pegawai) return collect();

        $pribadi = $pegawai->pribadi;
        if (!$pribadi) return collect();

        return $pribadi->pasangan;
    }

    public function findPegawaiByUserIdWithPribadi(int $userId): ?Pegawai
    {
        return Pegawai::query()->with('pribadi')->where('user_id', $userId)->first();
    }

    public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function findByIdAndUserId(int $id, int $userId): ?Pasangan
    {
        return Pasangan::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi.pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function create(array $data): Pasangan
    {
        return Pasangan::query()->create($data);
    }

    public function update(Pasangan $pasangan, array $data): bool
    {
        return $pasangan->update($data);
    }

    public function delete(Pasangan $pasangan): bool
    {
        return $pasangan->delete();
    }
}
