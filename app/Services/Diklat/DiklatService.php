<?php

namespace App\Services\Diklat;

use Illuminate\Http\UploadedFile;

class DiklatService
{
    public function __construct(
        private readonly AdminService $adminService,
        private readonly PegawaiService $pegawaiService,
        private readonly HrdService $hrdService,
        private readonly DirekturService $direkturService,
    ) {
    }

    public function getPayloadByRole(string $role, int $userId): ?array
    {
        return match ($role) {
            'admin' => $this->adminService->build($userId),
            'pegawai' => $this->pegawaiService->build($userId),
            'hrd' => $this->hrdService->build($userId),
            'direktur' => $this->direkturService->build($userId),
            default => null,
        };
    }

    public function createPegawaiDiklat(int $userId, array $payload, ?UploadedFile $sertifFile = null): array
    {
        return $this->pegawaiService->create($userId, $payload, $sertifFile);
    }

    public function updatePegawaiDiklat(int $diklatId, int $userId, array $payload, ?UploadedFile $sertifFile = null): array
    {
        return $this->pegawaiService->update($diklatId, $userId, $payload, $sertifFile);
    }

    public function deletePegawaiDiklat(int $diklatId, int $userId): array
    {
        return $this->pegawaiService->delete($diklatId, $userId);
    }

    public function uploadLaporanPegawai(int $diklatId, int $userId, array $payload, ?UploadedFile $laporanFile = null): array
    {
        return $this->pegawaiService->uploadLaporan($diklatId, $userId, $payload, $laporanFile);
    }

    public function getAllDiklat(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->hrdService->getAllDiklat();
    }

    public function createHrdDiklat(int $userId, array $payload): array
    {
        return $this->hrdService->createMasterDiklat($userId, $payload);
    }

    public function updateHrdDiklat(int $diklatId, int $userId, array $payload): array
    {
        return $this->hrdService->updateMasterDiklat($diklatId, $userId, $payload);
    }

    public function getPesertaDiklat(int $diklatId): array
    {
        return $this->hrdService->getPesertaDiklat($diklatId);
    }

    public function syncPesertaDiklat(int $diklatId, array $pegawaiIds): array
    {
        return $this->hrdService->syncPesertaDiklat($diklatId, $pegawaiIds);
    }

    public function getDiklatMenungguKelayakan(): array
    {
        return $this->hrdService->getDiklatMenungguKelayakan();
    }

    public function getDiklatMenungguValidasi(): array
    {
        return $this->hrdService->getDiklatMenungguValidasi();
    }

    public function updateStatusKelayakan(int $jadwalId, bool $isLayak): array
    {
        return $this->hrdService->updateStatusKelayakan($jadwalId, $isLayak);
    }

    public function updateStatusValidasi(int $jadwalId, bool $isValid): array
    {
        return $this->hrdService->updateStatusValidasi($jadwalId, $isValid);
    }
}
