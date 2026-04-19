<?php

namespace App\Repositories\Profile;

use App\Models\PegawaiPribadi;
use App\Models\PerubahanData;
use App\Models\User;

class PegawaiProfileRepository
{
    public function findUserWithPegawaiProfileRelations(int $userId): ?User
    {
        return User::query()
            ->with([
                'pegawai.jenisPegawai',
                'pegawai.profesi',
                'pegawai.pangkat',
                'pegawai.golonganRuang',
                'pegawai.pribadi',
                'pegawai.profesiPegawai.profesi',
                'pegawai.jabatanPegawai.jabatan',
                'pegawai.unitKerjaPegawai.unitKerja',
                'pegawai.pangkatPegawai.pangkat',
                'pegawai.golonganRuangPegawai.golonganRuang',
            ])
            ->find($userId);
    }

    public function findLatestProfileChangeRequestByUserId(int $userId): ?PerubahanData
    {
        return PerubahanData::query()
            ->where('by_user', $userId)
            ->where('fitur', 'profile')
            ->orderByDesc('id')
            ->first();
    }

    public function findUserWithPegawaiPribadiById(int $userId): ?User
    {
        return User::query()
            ->with(['pegawai.pribadi'])
            ->find($userId);
    }

    public function createPegawaiPribadi(int $pegawaiId): PegawaiPribadi
    {
        return PegawaiPribadi::query()->create([
            'pegawai_id' => $pegawaiId,
        ]);
    }

    public function savePegawaiPribadi(PegawaiPribadi $pegawaiPribadi): PegawaiPribadi
    {
        $pegawaiPribadi->save();

        return $pegawaiPribadi;
    }
}
