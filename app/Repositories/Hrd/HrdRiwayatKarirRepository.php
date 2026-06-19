<?php

namespace App\Repositories\Hrd;

use App\Models\JabatanPegawai;
use App\Models\Pangkat;
use App\Models\PangkatPegawai;
use App\Models\PenugasanKlinis;
use App\Models\Pegawai;
use App\Models\Sip;
use App\Models\StrPegawai;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class HrdRiwayatKarirRepository
{
    public function findPegawaiById(int $pegawaiId): ?Pegawai
    {
        return Pegawai::query()->where('id', $pegawaiId)->first();
    }

    // ── Jabatan ───────────────────────────────────────────────────────────────

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

    // ── STR ───────────────────────────────────────────────────────────────────

    public function getStrByPegawaiId(int $pegawaiId): Collection
    {
        return StrPegawai::query()->where('pegawai_id', $pegawaiId)->orderByDesc('id')->get();
    }

    public function findStrByIdAndPegawaiId(int $id, int $pegawaiId): ?StrPegawai
    {
        return StrPegawai::query()->where('id', $id)->where('pegawai_id', $pegawaiId)->first();
    }

    public function createStr(Pegawai $pegawai, array $data): StrPegawai
    {
        $str = new StrPegawai($data);
        $pegawai->str()->save($str);
        return $str;
    }

    public function updateStr(StrPegawai $str, array $data): StrPegawai
    {
        $str->update($data);
        return $str->fresh();
    }

    public function deleteStr(StrPegawai $str): void
    {
        $str->delete();
    }

    // ── SIP ───────────────────────────────────────────────────────────────────

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

    // ── Penugasan Klinis ──────────────────────────────────────────────────────

    public function getPenugasanKlinisByPegawaiId(int $pegawaiId): Collection
    {
        return PenugasanKlinis::query()->where('pegawai_id', $pegawaiId)->orderByDesc('id')->get();
    }

    public function findPenugasanKlinisByIdAndPegawaiId(int $id, int $pegawaiId): ?PenugasanKlinis
    {
        return PenugasanKlinis::query()->where('id', $id)->where('pegawai_id', $pegawaiId)->first();
    }

    public function createPenugasanKlinis(Pegawai $pegawai, array $data): PenugasanKlinis
    {
        $pk = new PenugasanKlinis($data);
        $pegawai->penugasanKlinis()->save($pk);
        return $pk;
    }

    public function updatePenugasanKlinis(PenugasanKlinis $pk, array $data): PenugasanKlinis
    {
        $pk->update($data);
        return $pk->fresh();
    }

    public function deletePenugasanKlinis(PenugasanKlinis $pk): void
    {
        $pk->delete();
    }

    // ── Pangkat ───────────────────────────────────────────────────────────────

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
