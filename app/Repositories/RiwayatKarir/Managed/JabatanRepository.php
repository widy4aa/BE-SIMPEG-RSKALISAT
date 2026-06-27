<?php

namespace App\Repositories\RiwayatKarir\Managed;

use App\Models\JabatanPegawai;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class JabatanRepository
{
    public function findPegawaiById(int $pegawaiId): ?Pegawai
    {
        return Pegawai::query()->where('id', $pegawaiId)->first();
    }

    public function getJabatanByPegawaiId(int $pegawaiId): Collection
    {
        return \App\Models\JabatanPegawai::query()
            ->with(['jabatan.unitKerja'])
            ->where('pegawai_id', $pegawaiId)
            ->orderByDesc('id')
            ->get();
    }

    public function findJabatanByIdAndPegawaiId(int $id, int $pegawaiId): ?JabatanPegawai
    {
        return JabatanPegawai::query()
            ->where('id', $id)
            ->where('pegawai_id', $pegawaiId)
            ->with(['jabatan.unitKerja'])
            ->first();
    }

    public function createJabatanAndPivot(Pegawai $pegawai, array $jabatanData, array $pivotData): JabatanPegawai
    {
        $jabatan = \App\Models\Jabatan::create($jabatanData);
        $pivotData['jabatan_id'] = $jabatan->id;
        return $pegawai->jabatanPegawai()->create($pivotData);
    }

    public function updateJabatanAndPivot(JabatanPegawai $jp, array $jabatanData, array $pivotData): JabatanPegawai
    {
        if (!empty($jabatanData) && $jp->jabatan) {
            $jp->jabatan->update($jabatanData);
        }
        if (!empty($pivotData)) {
            $jp->update($pivotData);
        }
        return $jp->fresh('jabatan.unitKerja');
    }

    public function deleteJabatanAndPivot(JabatanPegawai $jp): void
    {
        $jabatan = $jp->jabatan;
        $jp->delete();
        if ($jabatan) {
            $jabatan->delete();
        }
    }
}
