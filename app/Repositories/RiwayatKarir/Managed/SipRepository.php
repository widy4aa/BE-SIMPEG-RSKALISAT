<?php

namespace App\Repositories\RiwayatKarir\Managed;

use App\Models\Pegawai;
use App\Models\Sip;
use Illuminate\Support\Collection;

class SipRepository
{
    public function findPegawaiById(int $pegawaiId): ?Pegawai
    {
        return Pegawai::query()->where('id', $pegawaiId)->first();
    }

    public function getSipByPegawaiId(int $pegawaiId): Collection
    {
        return Sip::query()->with('jenisSip')->where('pegawai_id', $pegawaiId)->orderByDesc('id')->get();
    }

    public function findSipByIdAndPegawaiId(int $id, int $pegawaiId): ?Sip
    {
        return Sip::query()->with('jenisSip')->where('id', $id)->where('pegawai_id', $pegawaiId)->first();
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
