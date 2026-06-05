<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\PegawaiPribadi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPegawaiRepository
{
    public function getAllPegawai(): Collection
    {
        return Pegawai::with([
            'user',
            'pribadi',
            'profesi',
            'jabatan.unitKerja'
        ])->get();
    }

    public function getPaginatedPegawai(int $perPage = 10, array $filters = [])
    {
        $query = Pegawai::query()->with([
            'user',
            'pribadi',
            'jenisPegawai',
            'profesi',
            'profesiPegawai.profesi',
            'jabatan.unitKerja'
        ]);

        $this->applyPegawaiFilters($query, $filters);

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function getPegawaiDetail(int $pegawaiId): ?Pegawai
    {
        return Pegawai::with([
            'user',
            'pribadi',
            'profesi',
            'jabatan.unitKerja',
            'jenisPegawai',
            'golonganRuang',
            'pangkat',
            'jadwalDiklat.diklat.kategoriDiklat',
            'jadwalDiklat.diklat.jenisDiklat',
            'jabatanPegawai.jabatan.unitKerja',
            'str',
            'sip.jenisSip',
            'penugasanKlinis',
            'pasangan',
            'anak',
            'orangTua',
            'kontakDarurat',
            'tanggunganLain'
        ])->find($pegawaiId);
    }

    public function createPegawaiData(array $data): Pegawai
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['nik'],
                'password' => Hash::make($data['password']),
                'role' => 'pegawai',
                'is_active' => true,
            ]);

            $pegawai = Pegawai::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'nama' => $data['nama'],
            ]);

            PegawaiPribadi::create([
                'pegawai_id' => $pegawai->id,
            ]);

            return $pegawai;
        });
    }

    public function changeRole(int $pegawaiId, array $data): Pegawai
    {
        $pegawai = Pegawai::with('user')->findOrFail($pegawaiId);
        
        if (isset($data['role']) && $pegawai->user) {
            $pegawai->user->role = $data['role'];
            $pegawai->user->save();
        }

        if (isset($data['status_pegawai'])) {
            $pegawai->status_pegawai = $data['status_pegawai'];
            $pegawai->save();
        }

        return $pegawai;
    }

    public function countUsersByRole(string $role): int
    {
        return User::query()->where('role', $role)->count();
    }

    private function applyPegawaiFilters(Builder $query, array $filters): void
    {
        $search = $this->filledString($filters['search'] ?? null);
        if ($search !== null) {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhereHas('profesi', function (Builder $query) use ($search): void {
                        $query->where('nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('profesiPegawai.profesi', function (Builder $query) use ($search): void {
                        $query->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $statusKelengkapan = $this->filledString($filters['status_kelengkapan'] ?? null);
        if ($statusKelengkapan === 'lengkap') {
            $this->whereProfileComplete($query);
        } elseif ($statusKelengkapan === 'belum-lengkap') {
            $this->whereProfileIncomplete($query);
        }

        $jenisPegawai = $this->filledString($filters['jenis_pegawai'] ?? null);
        if ($jenisPegawai !== null) {
            $query->whereHas('jenisPegawai', function (Builder $query) use ($jenisPegawai): void {
                $query->where('nama', 'like', "%{$jenisPegawai}%");
            });
        }

        $pendidikan = $this->filledString($filters['pendidikan'] ?? null);
        if ($pendidikan !== null) {
            $query->whereHas('pribadi', function (Builder $query) use ($pendidikan): void {
                $query->where('pendidikan_terakhir', 'like', "%{$pendidikan}%");
            });
        }

        $statusPegawai = $this->filledString($filters['status_pegawai'] ?? null);
        if ($statusPegawai !== null) {
            $query->where('status_pegawai', $statusPegawai);
        }

        $profesi = $this->filledString($filters['profesi'] ?? null);
        if ($profesi !== null) {
            $query->where(function (Builder $query) use ($profesi): void {
                $query->whereHas('profesi', function (Builder $query) use ($profesi): void {
                    $query->where('nama', 'like', "%{$profesi}%");
                })->orWhereHas('profesiPegawai.profesi', function (Builder $query) use ($profesi): void {
                    $query->where('nama', 'like', "%{$profesi}%");
                });
            });
        }
    }

    private function whereProfileComplete(Builder $query): void
    {
        $query->whereNotNull('nik')
            ->where('nik', '!=', '')
            ->whereNotNull('jenis_pegawai_id')
            ->whereNotNull('profesi_id')
            ->whereNotNull('tgl_masuk');

        $query->whereHas('pribadi', function (Builder $query): void {
            $query->whereNotNull('tanggal_lahir');

            foreach (['jenis_kelamin', 'agama', 'alamat', 'no_telp', 'pendidikan_terakhir'] as $field) {
                $query->whereNotNull($field)
                    ->where($field, '!=', '');
            }
        });
    }

    private function whereProfileIncomplete(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query->orWhereNull('nik')
                ->orWhere('nik', '')
                ->orWhereNull('jenis_pegawai_id')
                ->orWhereNull('profesi_id')
                ->orWhereNull('tgl_masuk');

            $query->orWhereDoesntHave('pribadi')
                ->orWhereHas('pribadi', function (Builder $query): void {
                    $query->orWhereNull('tanggal_lahir');

                    foreach (['jenis_kelamin', 'agama', 'alamat', 'no_telp', 'pendidikan_terakhir'] as $field) {
                        $query->orWhereNull($field)
                            ->orWhere($field, '');
                    }
                });
        });
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
