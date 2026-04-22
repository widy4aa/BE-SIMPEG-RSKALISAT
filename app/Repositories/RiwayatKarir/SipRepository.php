<?php

namespace App\Repositories\RiwayatKarir;

use App\Models\Pegawai;
use App\Models\Sip;

class SipRepository
{
    public function findPegawaiByUserIdWithSip(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->with(['sip.jenisSip'])
            ->where('user_id', $userId)
            ->first();
    }

    public function findSipByIdAndUserId(int $id, int $userId): ?Sip
    {
        return Sip::query()
            ->where('id', $id)
            ->whereHas('pegawai', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['jenisSip'])
            ->first();
    }

    public function createSip(Pegawai $pegawai, array $data): Sip
    {
        $sip = new Sip($data);
        $pegawai->sip()->save($sip);
        return $sip->load('jenisSip');
    }

    public function updateSip(Sip $sip, array $data): Sip
    {
        $sip->update($data);
        return $sip->fresh('jenisSip');
    }

    public function deleteSip(Sip $sip): void
    {
        $sip->delete();
    }
}
