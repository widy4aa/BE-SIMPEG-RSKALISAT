<?php

namespace App\Repositories\Hrd;

use App\Models\Anak;
use App\Models\KontakDarurat;
use App\Models\OrangTua;
use App\Models\Pasangan;
use App\Models\PegawaiPribadi;
use App\Models\TanggunganLain;
use Illuminate\Support\Collection;

class HrdKeluargaRepository
{
    public function findPribadiByPegawaiId(int $pegawaiId): ?PegawaiPribadi
    {
        return PegawaiPribadi::query()->where('pegawai_id', $pegawaiId)->first();
    }

    public function createPribadiIfNotExists(int $pegawaiId): PegawaiPribadi
    {
        return $this->findPribadiByPegawaiId($pegawaiId)
            ?? PegawaiPribadi::query()->create(['pegawai_id' => $pegawaiId]);
    }

    // ── Pasangan ──────────────────────────────────────────────────────────────

    public function getPasanganByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return Pasangan::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findPasanganByIdAndPegawaiId(int $id, int $pegawaiId): ?Pasangan
    {
        return Pasangan::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createPasangan(array $data): Pasangan
    {
        return Pasangan::query()->create($data);
    }

    public function updatePasangan(Pasangan $pasangan, array $data): Pasangan
    {
        $pasangan->update($data);
        return $pasangan->fresh();
    }

    public function deletePasangan(Pasangan $pasangan): void
    {
        $pasangan->delete();
    }

    // ── Anak ──────────────────────────────────────────────────────────────────

    public function getAnakByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return Anak::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findAnakByIdAndPegawaiId(int $id, int $pegawaiId): ?Anak
    {
        return Anak::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createAnak(array $data): Anak
    {
        return Anak::query()->create($data);
    }

    public function updateAnak(Anak $anak, array $data): Anak
    {
        $anak->update($data);
        return $anak->fresh();
    }

    public function deleteAnak(Anak $anak): void
    {
        $anak->delete();
    }

    // ── Orang Tua ─────────────────────────────────────────────────────────────

    public function getOrangTuaByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return OrangTua::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findOrangTuaByIdAndPegawaiId(int $id, int $pegawaiId): ?OrangTua
    {
        return OrangTua::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createOrangTua(array $data): OrangTua
    {
        return OrangTua::query()->create($data);
    }

    public function updateOrangTua(OrangTua $orangTua, array $data): OrangTua
    {
        $orangTua->update($data);
        return $orangTua->fresh();
    }

    public function deleteOrangTua(OrangTua $orangTua): void
    {
        $orangTua->delete();
    }

    // ── Kontak Darurat ────────────────────────────────────────────────────────

    public function getKontakDaruratByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return KontakDarurat::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findKontakDaruratByIdAndPegawaiId(int $id, int $pegawaiId): ?KontakDarurat
    {
        return KontakDarurat::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createKontakDarurat(array $data): KontakDarurat
    {
        return KontakDarurat::query()->create($data);
    }

    public function updateKontakDarurat(KontakDarurat $kontakDarurat, array $data): KontakDarurat
    {
        $kontakDarurat->update($data);
        return $kontakDarurat->fresh();
    }

    public function deleteKontakDarurat(KontakDarurat $kontakDarurat): void
    {
        $kontakDarurat->delete();
    }

    // ── Tanggungan Lain ───────────────────────────────────────────────────────

    public function getTanggunganLainByPegawaiId(int $pegawaiId): Collection
    {
        $pribadi = $this->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            return collect();
        }
        return TanggunganLain::query()->where('pegawai_pribadi_id', $pribadi->id)->get();
    }

    public function findTanggunganLainByIdAndPegawaiId(int $id, int $pegawaiId): ?TanggunganLain
    {
        return TanggunganLain::query()
            ->where('id', $id)
            ->whereHas('pegawaiPribadi', fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->first();
    }

    public function createTanggunganLain(array $data): TanggunganLain
    {
        return TanggunganLain::query()->create($data);
    }

    public function updateTanggunganLain(TanggunganLain $tanggungan, array $data): TanggunganLain
    {
        $tanggungan->update($data);
        return $tanggungan->fresh();
    }

    public function deleteTanggunganLain(TanggunganLain $tanggungan): void
    {
        $tanggungan->delete();
    }
}
