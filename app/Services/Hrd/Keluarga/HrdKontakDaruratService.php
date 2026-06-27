<?php

namespace App\Services\Hrd\Keluarga;

use InvalidArgumentException;

class HrdKontakDaruratService extends BaseHrdKeluargaService
{
    public function getAllKontakDarurat(int $pegawaiId): array
    {
        $items = $this->repository->getKontakDaruratByPegawaiId($pegawaiId)
            ->map(fn ($k) => $k->toArray())->values()->toArray();

        return ['label' => 'Data Kontak Darurat', 'total' => count($items), 'items' => $items];
    }

    public function createKontakDarurat(int $pegawaiId, array $data): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);
        $data['pegawai_pribadi_id'] = $pribadi->id;

        $result = $this->repository->createKontakDarurat($data);

        return ['id' => $result->id, 'nama_kontak' => $result->nama_kontak];
    }

    public function updateKontakDarurat(int $id, int $pegawaiId, array $data): array
    {
        $kontakDarurat = $this->repository->findKontakDaruratByIdAndPegawaiId($id, $pegawaiId);
        if ($kontakDarurat === null) {
            throw new InvalidArgumentException('Data kontak darurat tidak ditemukan.');
        }

        $updated = $this->repository->updateKontakDarurat($kontakDarurat, $data);

        return ['id' => $updated->id, 'nama_kontak' => $updated->nama_kontak];
    }

    public function deleteKontakDarurat(int $id, int $pegawaiId): array
    {
        $kontakDarurat = $this->repository->findKontakDaruratByIdAndPegawaiId($id, $pegawaiId);
        if ($kontakDarurat === null) {
            throw new InvalidArgumentException('Data kontak darurat tidak ditemukan.');
        }

        $this->repository->deleteKontakDarurat($kontakDarurat);

        return ['id' => $id];
    }
}
