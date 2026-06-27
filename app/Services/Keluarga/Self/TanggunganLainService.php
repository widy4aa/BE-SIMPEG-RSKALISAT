<?php

namespace App\Services\Keluarga\Self;

use App\Repositories\Keluarga\Self\TanggunganLainRepository;
use InvalidArgumentException;

class TanggunganLainService
{
    public function __construct(private readonly TanggunganLainRepository $repository) {}

    public function getAllByUserId(int $userId): array
    {
        $items = $this->repository->getByUserId($userId)
            ->map(fn ($item) => $item->toArray())
            ->toArray();

        return [
            'welcome' => 'Data tanggungan lain berhasil diambil.',
            'summary' => [
                'label' => 'Data Tanggungan Lain',
                'total' => count($items),
                'items' => $items,
            ],
        ];
    }

    public function createByUserId(int $userId, array $payload): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->repository->findPegawaiByUserIdWithPribadi($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $pribadi = $pegawai->pribadi;
        if ($pribadi === null) {
            $pribadi = $this->repository->createPegawaiPribadi((int) $pegawai->id);
        }

        $payload['pegawai_pribadi_id'] = $pribadi->id;

        $item = $this->repository->create($payload);

        return [
            'id'   => $item->id,
            'nama' => $item->nama,
        ];
    }

    public function updateById(int $id, int $userId, array $payload): array
    {
        $item = $this->repository->findByIdAndUserId($id, $userId);

        if (!$item) {
            throw new InvalidArgumentException('Data tanggungan lain tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->repository->update($item, $payload);

        return [
            'id'   => $item->id,
            'nama' => $item->nama,
        ];
    }

    public function deleteById(int $id, int $userId): array
    {
        $item = $this->repository->findByIdAndUserId($id, $userId);

        if (!$item) {
            throw new InvalidArgumentException('Data tanggungan lain tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->repository->delete($item);

        return ['id' => $id];
    }
}
