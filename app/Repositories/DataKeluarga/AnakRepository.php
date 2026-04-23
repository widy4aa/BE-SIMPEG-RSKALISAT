<?php

namespace App\Repositories\DataKeluarga;

use App\Models\Anak;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;

class AnakRepository
{
    public function getAnakByUserId(int $userId)
    {
        $pegawai = Pegawai::where('user_id', $userId)->first();
        if (!$pegawai) return collect();

        $pribadi = $pegawai->pribadi;
        if (!$pribadi) return collect();

        return $pribadi->anak;
    }

    public function findPegawaiByUserIdWithPribadi(int $userId): ?Pegawai
    {
        return Pegawai::query()->with('pribadi')->where('user_id', $userId)->first();
    }

    public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function findByIdAndUserId(int $id, int $userId): ?Anak
    {
        return Anak::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi.pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function create(array $data): Anak
    {
        return Anak::query()->create($data);
    }

    public function update(Anak $anak, array $data): bool
    {
        return $anak->update($data);
    }

    public function delete(Anak $anak): bool
    {
        return $anak->delete();
    }
}
