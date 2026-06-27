<?php

namespace App\Repositories\RiwayatKarir\Managed;

use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\Pegawai;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PangkatRepository
{
    public function findPegawaiById(int $pegawaiId): ?Pegawai
    {
        return Pegawai::query()->where('id', $pegawaiId)->first();
    }

    public function getPangkatByPegawaiId(int $pegawaiId): Collection
    {
        return PangkatPegawai::query()
            ->with('pangkat')
            ->where('pegawai_id', $pegawaiId)
            ->orderByDesc('id')
            ->get();
    }

    public function findPangkatByIdAndPegawaiId(int $id, int $pegawaiId): ?PangkatPegawai
    {
        return PangkatPegawai::query()
            ->with('pangkat')
            ->where('id', $id)
            ->where('pegawai_id', $pegawaiId)
            ->first();
    }

    public function createPangkatAndPivot(Pegawai $pegawai, array $pangkatData, array $pivotData): PangkatPegawai
    {
        return DB::transaction(function () use ($pegawai, $pangkatData, $pivotData) {
            $pangkat = Pangkat::create($pangkatData);
            $pp = new PangkatPegawai($pivotData);
            $pp->pangkat_id = $pangkat->id;
            $pegawai->pangkatPegawai()->save($pp);
            return $pp->load('pangkat');
        });
    }

    public function updatePangkatAndPivot(PangkatPegawai $pp, array $pangkatData, array $pivotData): PangkatPegawai
    {
        return DB::transaction(function () use ($pp, $pangkatData, $pivotData) {
            if (!empty($pangkatData) && $pp->pangkat) {
                $pp->pangkat->update($pangkatData);
            }
            if (!empty($pivotData)) {
                $pp->update($pivotData);
            }
            return $pp->fresh('pangkat');
        });
    }

    public function deletePangkatAndPivot(PangkatPegawai $pp): void
    {
        DB::transaction(function () use ($pp) {
            if ($pp->pangkat) {
                $pp->pangkat->delete();
            }
            $pp->delete();
        });
    }
}
