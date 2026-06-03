<?php

namespace App\Services\Pegawai;

class PegawaiService
{
    public function __construct(
        private readonly AdminPegawaiService $adminService,
        private readonly HrdPegawaiService $hrdService,
        private readonly DirekturPegawaiService $direkturService,
    ) {
    }

    public function getPayloadByRole(string $role): ?array
    {
        return match ($role) {
            'admin' => $this->adminService->getPegawaiData(),
            'hrd' => $this->hrdService->getPegawaiData(),
            'direktur' => $this->direkturService->getPegawaiData(),
            default => null,
        };
    }

    public function getPegawaiDetailData(int $pegawaiId): array
    {
        // Available for admin, hrd, direktur
        return $this->adminService->getPegawaiDetailData($pegawaiId);
    }

    public function createPegawai(string $role, array $data): ?array
    {
        return match ($role) {
            'admin' => $this->adminService->createPegawai($data),
            default => null,
        };
    }

    public function changeRole(string $adminRole, int $pegawaiId, array $data, int $adminUserId): ?array
    {
        return match ($adminRole) {
            'admin' => $this->adminService->changeRole($pegawaiId, $data, $adminUserId),
            default => null,
        };
    }
}
