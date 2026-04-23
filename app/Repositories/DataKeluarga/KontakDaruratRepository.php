<?php

namespace App\Repositories\DataKeluarga;

use App\Models\KontakDarurat;
use App\Models\Pegawai;
use App\Models\PegawaiPribadi;

class KontakDaruratRepository
{
    public function getKontakDaruratByUserId(int $userId)
    {
        $pegawai = Pegawai::where('user_id', $userId)->first();
        if (!$pegawai) return collect();

        $pribadi = $pegawai->pribadi;
        if (!$pribadi) return collect();

        return $pribadi->kontakDarurat;
    }

    public function findPegawaiByUserIdWithPribadi(int $userId): ?Pegawai
    {
        return Pegawai::query()->with('pribadi')->where('user_id', $userId)->first();
    }

    public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    public function findByIdAndUserId(int $id, int $userId): ?KontakDarurat
    {
        return KontakDarurat::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi.pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function create(array $data): KontakDarurat
    {
        return KontakDarurat::query()->create($data);
    }

    public function update(KontakDarurat $kontakDarurat, array $data): bool
    {
        return $kontakDarurat->update($data);
    }

    public function delete(KontakDarurat $kontakDarurat): bool
    {
        return $kontakDarurat->delete();
    }
}
