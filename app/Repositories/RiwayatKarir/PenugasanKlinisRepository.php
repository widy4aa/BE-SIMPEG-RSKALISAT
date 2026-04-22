<?php

namespace App\Repositories\RiwayatKarir;

use App\Models\Pegawai;
use App\Models\PenugasanKlinis;

class PenugasanKlinisRepository
{
    public function findPegawaiByUserIdWithPenugasanKlinis(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with(['penugasanKlinis'])
            ->where('user_id', $userId)
            ->first();
    }

    public function findPenugasanKlinisByIdAndUserId(int $id, int $userId): ?PenugasanKlinis
    {
        return PenugasanKlinis::query()
            ->where('id', $id)
            ->whereHas('pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->first();
    }

    public function createPenugasanKlinis(Pegawai $pegawai, array $data): PenugasanKlinis
    {
        $penugasanKlinis = new PenugasanKlinis($data);
        $pegawai->penugasanKlinis()->save($penugasanKlinis);
        return $penugasanKlinis;
    }

    public function updatePenugasanKlinis(PenugasanKlinis $penugasanKlinis, array $data): PenugasanKlinis
    {
        $penugasanKlinis->update($data);
        return $penugasanKlinis->fresh();
    }

    public function deletePenugasanKlinis(PenugasanKlinis $penugasanKlinis): void
    {
        $penugasanKlinis->delete();
    }
}
