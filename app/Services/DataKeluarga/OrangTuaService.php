<?php

namespace App\Services\DataKeluarga;

use App\Repositories\DataKeluarga\OrangTuaRepository;
use InvalidArgumentException;

class OrangTuaService
{
    public function __construct(private readonly OrangTuaRepository $repository) {}

    public function getAllByUserId(int $userId): array
    {
        $orangTua = $this->repository->getOrangTuaByUserId($userId);
        
        $items = $orangTua->map(function ($item) {
            return $item->toArray();
        })->toArray();

        return [
            'welcome' => 'Data orang tua berhasil diambil.',
            'summary' => [
                'label' => 'Data Orang Tua',
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

        $newOrangTua = $this->repository->create($payload);

        return [
            'id' => $newOrangTua->id,
            'nama_ayah' => $newOrangTua->nama_ayah,
            'nama_ibu' => $newOrangTua->nama_ibu,
        ];
    }

    public function updateById(int $id, int $userId, array $payload): array
    {
        $orangTua = $this->repository->findByIdAndUserId($id, $userId);

        if (!$orangTua) {
            throw new InvalidArgumentException('Data orang tua tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->repository->update($orangTua, $payload);

        return [
            'id' => $orangTua->id,
            'nama_ayah' => $orangTua->nama_ayah,
            'nama_ibu' => $orangTua->nama_ibu,
        ];
    }

    public function deleteById(int $id, int $userId): array
    {
        $orangTua = $this->repository->findByIdAndUserId($id, $userId);

        if (!$orangTua) {
            throw new InvalidArgumentException('Data orang tua tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->repository->delete($orangTua);

        return [
            'id' => $id,
        ];
    }
}
