<?php

namespace App\Services\Keluarga\Managed;

use App\Repositories\Keluarga\Managed\OrangTuaRepository;
use InvalidArgumentException;

class OrangTuaService extends BaseKeluargaService
{
    public function __construct(protected readonly OrangTuaRepository $repository) {}

    public function getAllOrangTua(int $pegawaiId): array
    {
        $items = $this->repository->getOrangTuaByPegawaiId($pegawaiId)
            ->map(fn ($o) => $o->toArray())->values()->toArray();

        return ['label' => 'Data Orang Tua', 'total' => count($items), 'items' => $items];
    }

    public function createOrangTua(int $pegawaiId, array $data): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);
        $data['pegawai_pribadi_id'] = $pribadi->id;

        $result = $this->repository->createOrangTua($data);

        return ['id' => $result->id, 'nama_ayah' => $result->nama_ayah, 'nama_ibu' => $result->nama_ibu];
    }

    public function updateOrangTua(int $id, int $pegawaiId, array $data): array
    {
        $orangTua = $this->repository->findOrangTuaByIdAndPegawaiId($id, $pegawaiId);
        if ($orangTua === null) {
            throw new InvalidArgumentException('Data orang tua tidak ditemukan.');
        }

        $updated = $this->repository->updateOrangTua($orangTua, $data);

        return ['id' => $updated->id, 'nama_ayah' => $updated->nama_ayah, 'nama_ibu' => $updated->nama_ibu];
    }

    public function deleteOrangTua(int $id, int $pegawaiId): array
    {
        $orangTua = $this->repository->findOrangTuaByIdAndPegawaiId($id, $pegawaiId);
        if ($orangTua === null) {
            throw new InvalidArgumentException('Data orang tua tidak ditemukan.');
        }

        $this->repository->deleteOrangTua($orangTua);

        return ['id' => $id];
    }
}
