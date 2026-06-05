<?php

namespace App\Repositories\Diklat;

use App\Models\Diklat;
use App\Models\JenisBiaya;
use App\Models\JenisDiklat;
use App\Models\KategoriDiklat;
use App\Models\ListJadwalDiklat;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    public function getAllMasterDiklat(): Collection
    {
        return Diklat::query()
            ->with([
                'kategoriDiklat',
                'jenisDiklat',
                'jenisBiaya',
                'createdByPegawai',
            ])
            ->withCount('jadwalPeserta')
            ->orderByDesc('id')
            ->get();
    }

    public function getPaginatedRiwayatDiklatByPegawaiId(int $pegawaiId, int $perPage = 7, array $filters = [])
    {
        $query = ListJadwalDiklat::query()
            ->with([
                'diklat.kategoriDiklat',
                'diklat.jenisDiklat',
                'diklat.jenisBiaya',
                'diklat.createdByPegawai',
            ])
            ->where('pegawai_id', $pegawaiId);

        $this->applyDiklatRelationFilters($query, $filters);

        return $query
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function getPaginatedMasterDiklat(int $perPage = 7, array $filters = [])
    {
        $query = Diklat::query()
            ->with([
                'kategoriDiklat',
                'jenisDiklat',
                'jenisBiaya',
                'createdByPegawai',
            ])
            ->withCount('jadwalPeserta');

        $this->applyDiklatFilters($query, $filters);

        return $query
            ->orderByDesc('id')
            ->paginate($perPage);
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

    public function getRekapLaporanDiklatInternal(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): Collection
    {
        return Diklat::query()
            ->with([
                'jenisDiklat',
                'jenisBiaya',
                'jadwalPeserta' => function ($query) {
                    $query->where('status_validasi', 'valid')
                          ->whereNotNull('sertif_file_path')
                          ->with('pegawai.jabatan.unitKerja');
                }
            ])
            ->withCount([
                'jadwalPeserta as total_peserta',
                'jadwalPeserta as total_peserta_validasi' => function ($query) {
                    $query->where('status_validasi', 'valid')
                          ->whereNotNull('sertif_file_path');
                }
            ])
            ->where('jenis_pelaksanaan', 'internal')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderByDesc('id')
            ->get();
    }

    private function applyDiklatRelationFilters(Builder $query, array $filters): void
    {
        $query->whereHas('diklat', function (Builder $query) use ($filters): void {
            $this->applyDiklatFilters($query, $filters);
        });
    }

    private function applyDiklatFilters(Builder $query, array $filters): void
    {
        $search = $this->filledString($filters['search'] ?? null);
        if ($search !== null) {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('penyelenggara', 'like', "%{$search}%")
                    ->orWhereHas('kategoriDiklat', function (Builder $query) use ($search): void {
                        $query->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jenisDiklat', function (Builder $query) use ($search): void {
                        $query->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $jenis = $this->filledString($filters['jenis'] ?? null);
        if ($jenis !== null) {
            $query->whereHas('jenisDiklat', function (Builder $query) use ($jenis): void {
                $query->where('nama', 'like', "%{$jenis}%");
            });
        }

        $status = $this->filledString($filters['status'] ?? null);
        if ($status !== null) {
            $today = Carbon::today()->toDateString();

            if ($status === 'mendatang') {
                $query->whereDate('tanggal_mulai', '>', $today);
            } elseif ($status === 'selesai') {
                $query->whereDate('tanggal_selesai', '<', $today);
            } elseif ($status === 'berlangsung') {
                $query->whereDate('tanggal_mulai', '<=', $today)
                    ->whereDate('tanggal_selesai', '>=', $today);
            }
        }
    }

    private function filledString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
