<?php

namespace App\Repositories\StrSip;

use App\Models\Sip;
use App\Models\StrPegawai;
use Illuminate\Support\Collection;

class StrSipRepository
{
    public function getAllStr(array $filters = []): Collection
    {
        $query = StrPegawai::query()->with(['pegawai.profesi']);

        if ($this->filled($filters['search'] ?? null)) {
            $query->whereHas('pegawai', fn ($q) => $q->where('nama', 'LIKE', '%'.$filters['search'].'%'));
        }

        $this->applyTanggalFilter($query, $filters);
        $this->applyStatusFilter($query, $filters);

        return $query->get();
    }

    public function getAllSip(array $filters = []): Collection
    {
        $query = Sip::query()->with(['pegawai.profesi', 'jenisSip']);

        if ($this->filled($filters['search'] ?? null)) {
            $query->whereHas('pegawai', fn ($q) => $q->where('nama', 'LIKE', '%'.$filters['search'].'%'));
        }

        if ($this->filled($filters['jenis_sip'] ?? null)) {
            $query->whereHas('jenisSip', fn ($q) => $q->where('nama', 'LIKE', '%'.$filters['jenis_sip'].'%'));
        }

        $this->applyTanggalFilter($query, $filters);
        $this->applyStatusFilter($query, $filters);

        return $query->get();
    }

    private function applyTanggalFilter(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        if ($this->filled($filters['tanggal_dari'] ?? null)) {
            $query->where('tanggal_kadaluarsa', '>=', $filters['tanggal_dari']);
        }

        if ($this->filled($filters['tanggal_sampai'] ?? null)) {
            $query->where('tanggal_kadaluarsa', '<=', $filters['tanggal_sampai']);
        }
    }

    private function applyStatusFilter(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        $status = strtolower(trim((string) ($filters['status'] ?? '')));

        if ($status === 'aktif') {
            $query->where('tanggal_kadaluarsa', '>', now()->addDays(30));
        } elseif ($status === 'hampir_habis') {
            $query->whereBetween('tanggal_kadaluarsa', [now(), now()->addDays(30)]);
        } elseif ($status === 'tidak_aktif') {
            $query->where(fn ($q) => $q->whereNull('tanggal_kadaluarsa')->orWhere('tanggal_kadaluarsa', '<', now()));
        }
    }

    private function filled(mixed $value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }
}
