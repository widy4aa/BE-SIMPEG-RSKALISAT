<?php

namespace App\Services\Dashboard;

class DashboardService
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
}
