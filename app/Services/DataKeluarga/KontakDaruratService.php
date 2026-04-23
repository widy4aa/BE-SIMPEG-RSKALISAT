<?php

namespace App\Services\DataKeluarga;

use App\Repositories\DataKeluarga\KontakDaruratRepository;
use InvalidArgumentException;

class KontakDaruratService
{
    public function __construct(private readonly KontakDaruratRepository $repository) {}

    public function getAllByUserId(int $userId): array
    {
        $kontakDarurat = $this->repository->getKontakDaruratByUserId($userId);
        
        $items = $kontakDarurat->map(function ($item) {
            return $item->toArray();
        })->toArray();

        return [
            'welcome' => 'Data kontak darurat berhasil diambil.',
            'summary' => [
                'label' => 'Data Kontak Darurat',
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

        $newKontakDarurat = $this->repository->create($payload);

        return [
            'id' => $newKontakDarurat->id,
            'nama_kontak' => $newKontakDarurat->nama_kontak,
        ];
    }

    public function updateById(int $id, int $userId, array $payload): array
    {
        $kontakDarurat = $this->repository->findByIdAndUserId($id, $userId);

        if (!$kontakDarurat) {
            throw new InvalidArgumentException('Data kontak darurat tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->repository->update($kontakDarurat, $payload);

        return [
            'id' => $kontakDarurat->id,
            'nama_kontak' => $kontakDarurat->nama_kontak,
        ];
    }

    public function deleteById(int $id, int $userId): array
    {
        $kontakDarurat = $this->repository->findByIdAndUserId($id, $userId);

        if (!$kontakDarurat) {
            throw new InvalidArgumentException('Data kontak darurat tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $this->repository->delete($kontakDarurat);

        return [
            'id' => $id,
        ];
    }
}
