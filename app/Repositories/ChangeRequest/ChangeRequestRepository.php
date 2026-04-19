<?php

namespace App\Repositories\ChangeRequest;

use App\Models\PerubahanData;
use Illuminate\Database\Eloquent\Collection;

class ChangeRequestRepository
{
    public function list(?string $status = null, ?string $fitur = null): Collection
    {
        $query = PerubahanData::query()
            ->with(['user.pegawai'])
            ->withCount('details')
            ->orderByDesc('id');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($fitur !== null && $fitur !== '') {
            $query->where('fitur', $fitur);
        }

        return $query->get();
    }

    public function findByIdWithDetails(int $id): ?PerubahanData
    {
        return PerubahanData::query()
            ->with(['user.pegawai', 'details'])
            ->find($id);
    }

    public function findByIdForAction(int $id): ?PerubahanData
    {
        return PerubahanData::query()
            ->with(['user.pegawai.pribadi', 'details'])
            ->lockForUpdate()
            ->find($id);
    }

    public function save(PerubahanData $perubahanData): PerubahanData
    {
        $perubahanData->save();

        return $perubahanData;
    }
}
