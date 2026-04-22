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
}
