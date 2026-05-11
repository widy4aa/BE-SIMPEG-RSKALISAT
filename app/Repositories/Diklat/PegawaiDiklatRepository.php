<?php

namespace App\Repositories\Diklat;

use App\Models\Diklat;
use App\Models\JenisBiaya;
use App\Models\JenisDiklat;
use App\Models\KategoriDiklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use Illuminate\Support\Collection;

class PegawaiDiklatRepository
{
    public function findPegawaiByUserId(int $userId): ?Pegawai
    {
        return Pegawai::query()
            ->where('user_id', $userId)
            ->first();
    }

    public function findDiklatById(int $diklatId): ?Diklat
    {
        return Diklat::find($diklatId);
    }

    public function getRiwayatDiklatByPegawaiId(int $pegawaiId): Collection
    {
        return ListJadwalDiklat::query()
            ->with([
                'diklat.kategoriDiklat',
                'diklat.jenisDiklat',
                'diklat.jenisBiaya',
                'diklat.createdByPegawai',
            ])
            ->where('pegawai_id', $pegawaiId)
            ->orderByDesc('id')
            ->get();
    }

    public function getAllJadwalDiklat(): Collection
    {
        return ListJadwalDiklat::query()
            ->with([
                'diklat.kategoriDiklat',
                'diklat.jenisDiklat',
                'diklat.jenisBiaya',
                'diklat.createdByPegawai',
                'pegawai',
            ])
            ->orderByDesc('id')
            ->get();
    }

    public function firstOrCreateKategoriByNama(string $nama): KategoriDiklat
    {
        return KategoriDiklat::query()->firstOrCreate([
            'nama' => trim($nama),
        ]);
    }

    public function firstOrCreateJenisByNama(string $nama): JenisDiklat
    {
        return JenisDiklat::query()->firstOrCreate([
            'nama' => trim($nama),
        ]);
    }

    public function firstOrCreateJenisBiayaByNama(string $nama): JenisBiaya
    {
        return JenisBiaya::query()->firstOrCreate([
            'nama' => trim($nama),
        ]);
    }

    public function createDiklat(array $attributes): Diklat
    {
        return Diklat::query()->create($attributes);
    }

    public function createJadwalDiklat(array $attributes): ListJadwalDiklat
    {
        return ListJadwalDiklat::query()->create($attributes);
    }

    public function findJadwalByDiklatIdAndPegawaiId(int $diklatId, int $pegawaiId): ?ListJadwalDiklat
    {
        return ListJadwalDiklat::query()
            ->with([
                'diklat.kategoriDiklat',
                'diklat.jenisDiklat',
                'diklat.jenisBiaya',
            ])
            ->where('diklat_id', $diklatId)
            ->where('pegawai_id', $pegawaiId)
            ->first();
    }

    public function findJadwalById(int $jadwalId): ?ListJadwalDiklat
    {
        return ListJadwalDiklat::query()
            ->with(['diklat', 'pegawai'])
            ->whereKey($jadwalId)
            ->first();
    }

    public function saveDiklat(Diklat $diklat): bool
    {
        return $diklat->save();
    }

    public function saveJadwalDiklat(ListJadwalDiklat $jadwal): bool
    {
        return $jadwal->save();
    }

    public function deleteJadwalDiklat(ListJadwalDiklat $jadwal): ?bool
    {
        return $jadwal->delete();
    }

    public function countRemainingJadwalByDiklatId(int $diklatId): int
    {
        return ListJadwalDiklat::query()
            ->where('diklat_id', $diklatId)
            ->count();
    }

    public function deleteDiklat(Diklat $diklat): ?bool
    {
        return $diklat->delete();
    }

    public function getAllPegawaiWithUnitKerjaProfesi(): Collection
    {
        return Pegawai::query()
            ->with(['jabatan.unitKerja', 'profesi'])
            ->get();
    }

    public function getPesertaDiklatIds(int $diklatId): array
    {
        return ListJadwalDiklat::query()
            ->where('diklat_id', $diklatId)
            ->pluck('pegawai_id')
            ->toArray();
    }

    public function deleteJadwalNotInPegawaiIds(int $diklatId, array $pegawaiIds): void
    {
        ListJadwalDiklat::query()
            ->where('diklat_id', $diklatId)
            ->whereNotIn('pegawai_id', $pegawaiIds)
            ->delete();
    }

    public function getJadwalDiklatMenungguKelayakan(): Collection
    {
        return ListJadwalDiklat::query()
            ->with([
                'diklat.kategoriDiklat',
                'diklat.jenisDiklat',
                'diklat.jenisBiaya',
                'pegawai',
            ])
            ->whereNull('status_kelayakan')
            ->whereNotNull('sertif_file_path')
            ->orderByDesc('id')
            ->get();
    }

    public function getJadwalDiklatMenungguValidasi(): Collection
    {
        return ListJadwalDiklat::query()
            ->with([
                'diklat.kategoriDiklat',
                'diklat.jenisDiklat',
                'diklat.jenisBiaya',
                'pegawai',
            ])
            ->whereNotNull('sertif_file_path')
            ->whereNull('status_validasi')
            ->orderByDesc('id')
            ->get();
    }
}
