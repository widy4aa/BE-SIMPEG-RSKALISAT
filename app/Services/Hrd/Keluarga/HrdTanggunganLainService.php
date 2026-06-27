<?php

namespace App\Services\Hrd\Keluarga;

use InvalidArgumentException;

class HrdTanggunganLainService extends BaseHrdKeluargaService
{
    public function getAllTanggunganLain(int $pegawaiId): array
    {
        $items = $this->repository->getTanggunganLainByPegawaiId($pegawaiId)
            ->map(fn ($t) => $t->toArray())->values()->toArray();

        return ['label' => 'Data Tanggungan Lain', 'total' => count($items), 'items' => $items];
    }

    public function createTanggunganLain(int $pegawaiId, array $data): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);
        $data['pegawai_pribadi_id'] = $pribadi->id;

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = $this->normalizeBoolean($data['status_tanggungan']);
        }

        $result = $this->repository->createTanggunganLain($data);

        return ['id' => $result->id, 'nama' => $result->nama];
    }

    public function updateTanggunganLain(int $id, int $pegawaiId, array $data): array
    {
        $tanggungan = $this->repository->findTanggunganLainByIdAndPegawaiId($id, $pegawaiId);
        if ($tanggungan === null) {
            throw new InvalidArgumentException('Data tanggungan lain tidak ditemukan.');
        }

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = $this->normalizeBoolean($data['status_tanggungan']);
        }

        $updated = $this->repository->updateTanggunganLain($tanggungan, $data);

        return ['id' => $updated->id, 'nama' => $updated->nama];
    }

    public function deleteTanggunganLain(int $id, int $pegawaiId): array
    {
        $tanggungan = $this->repository->findTanggunganLainByIdAndPegawaiId($id, $pegawaiId);
        if ($tanggungan === null) {
            throw new InvalidArgumentException('Data tanggungan lain tidak ditemukan.');
        }

        $this->repository->deleteTanggunganLain($tanggungan);

        return ['id' => $id];
    }
}
