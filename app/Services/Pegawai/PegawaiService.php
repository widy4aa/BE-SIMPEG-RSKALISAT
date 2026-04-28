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
}
