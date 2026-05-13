<?php

namespace App\Repositories\StrSip;

use App\Models\Sip;
use App\Models\StrPegawai;
use Illuminate\Support\Collection;

class StrSipRepository
{
    public function getAllStr(): Collection
    {
        return StrPegawai::query()
            ->with(['pegawai.profesi'])
            ->get();
    }

    public function getAllSip(): Collection
    {
        return Sip::query()
            ->with(['pegawai.profesi', 'jenisSip'])
            ->get();
    }
}
