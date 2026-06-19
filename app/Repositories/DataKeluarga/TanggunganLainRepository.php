<?php

namespace App\Repositories\DataKeluarga;

use App\Models\Pegawai;
use App\Models\PegawaiPribadi;
use App\Models\TanggunganLain;

class TanggunganLainRepository
{
    public function getByUserId(int $userId)
    {
        $pegawai = Pegawai::where('user_id', $userId)->first();
        if (!$pegawai) return collect();

        $pribadi = $pegawai->pribadi;
        if (!$pribadi) return collect();

        return $pribadi->tanggunganLain;
    }

    public function findPegawaiByUserIdWithPribadi(int $userId): ?Pegawai
    {
        return Pegawai::query()->with('pribadi')->where('user_id', $userId)->first();
    }

    public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function findByIdAndUserId(int $id, int $userId): ?TanggunganLain
    {
        return TanggunganLain::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi.pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function create(array $data): TanggunganLain
    {
        return TanggunganLain::query()->create($data);
    }

    public function update(TanggunganLain $item, array $data): bool
    {
        return $item->update($data);
    }

    public function delete(TanggunganLain $item): bool
    {
        return $item->delete();
    }
}
