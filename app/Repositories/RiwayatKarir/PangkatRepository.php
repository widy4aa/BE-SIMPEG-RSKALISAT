<?php

namespace App\Repositories\RiwayatKarir;

use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class PangkatRepository
{
    public function findPegawaiByUserIdWithPangkat(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with(['pangkatPegawai.pangkat'])
            ->where('user_id', $userId)
            ->first();
    }

    public function findPangkatPegawaiByIdAndUserId(int $id, int $userId): ?PangkatPegawai
    {
        return PangkatPegawai::query()
            ->where('id', $id)
            ->whereHas('pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['pangkat'])
            ->first();
    }

    public function createPangkatAndPivot(Pegawai $pegawai, array $pangkatData, array $pivotData): PangkatPegawai
    {
        return DB::transaction(function () use ($pegawai, $pangkatData, $pivotData) {
            $pangkat = Pangkat::create($pangkatData);

            $pangkatPegawai = new PangkatPegawai($pivotData);
            $pangkatPegawai->pangkat_id = $pangkat->id;
            
            $pegawai->pangkatPegawai()->save($pangkatPegawai);

            return $pangkatPegawai->load('pangkat');
        });
    }

    public function updatePangkatAndPivot(PangkatPegawai $pangkatPegawai, array $pangkatData, array $pivotData): PangkatPegawai
    {
        return DB::transaction(function () use ($pangkatPegawai, $pangkatData, $pivotData) {
            if (!empty($pangkatData) && $pangkatPegawai->pangkat) {
                $pangkatPegawai->pangkat->update($pangkatData);
            }

            if (!empty($pivotData)) {
                $pangkatPegawai->update($pivotData);
            }

            return $pangkatPegawai->fresh('pangkat');
        });
    }

    public function deletePangkatAndPivot(PangkatPegawai $pangkatPegawai): void
    {
        DB::transaction(function () use ($pangkatPegawai) {
            if ($pangkatPegawai->pangkat) {
                $pangkatPegawai->pangkat->delete();
            }
            $pangkatPegawai->delete();
        });
    }
}
