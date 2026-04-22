<?php

namespace App\Repositories\RiwayatKarir;

use App\Models\Pegawai;

class JabatanRepository
{
    public function findPegawaiByUserIdWithJabatan(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with(['jabatanPegawai.jabatan.unitKerja'])
            ->where('user_id', $userId)
            ->first();
    }

    public function findJabatanPegawaiByIdAndUserId(int $id, int $userId): ?\App\Models\JabatanPegawai
    {
        return \App\Models\JabatanPegawai::query()
            ->where('id', $id)
            ->whereHas('pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['jabatan.unitKerja'])
            ->first();
    }

    public function createJabatanAndPivot(Pegawai $pegawai, array $jabatanData, array $pivotData): \App\Models\JabatanPegawai
    {
        $jabatan = \App\Models\Jabatan::create($jabatanData);

        $pivotData['jabatan_id'] = $jabatan->id;
        return $pegawai->jabatanPegawai()->create($pivotData);
    }

    public function updateJabatanAndPivot(\App\Models\JabatanPegawai $jabatanPegawai, array $jabatanData, array $pivotData): \App\Models\JabatanPegawai
    {
        if (!empty($jabatanData) && $jabatanPegawai->jabatan) {
            $jabatanPegawai->jabatan->update($jabatanData);
        }

        if (!empty($pivotData)) {
            $jabatanPegawai->update($pivotData);
        }

        return $jabatanPegawai->fresh('jabatan');
    }

    public function deleteJabatanAndPivot(\App\Models\JabatanPegawai $jabatanPegawai): bool
    {
        $jabatan = $jabatanPegawai->jabatan;
        $deleted = $jabatanPegawai->delete();
        if ($jabatan) {
            $jabatan->delete();
        }
        return $deleted;
    }
}
