<?php

namespace App\Services\Pegawai\Managed;

/**
 * Orchestrator service untuk domain Pegawai (Managed).
 * - Admin: gunakan AdminPegawaiService (CRUD lengkap)
 * - HRD + Direktur: gunakan PegawaiService (konsolidasi Kasus #2)
 */
class PegawaiOrchestratorService
{
    public function __construct(
        private readonly AdminPegawaiService $adminService,
        private readonly PegawaiService $managedService,
    ) {
    }

    public function getPayloadByRole(string $role, array $filters = []): ?array
    {
        return match ($role) {
            'admin'    => $this->adminService->getPegawaiData($filters),
            'hrd'      => $this->managedService->getPegawaiData($filters),
            'direktur' => $this->managedService->getPegawaiData($filters),
            default    => null,
        };
    }

    public function getPegawaiDetailData(int $pegawaiId): array
    {
        return $this->adminService->getPegawaiDetailData($pegawaiId);
    }

    public function getPegawaiDetailBagianData(int $pegawaiId, string $bagian, array $filters = []): array
    {
        return $this->adminService->getPegawaiDetailBagianData($pegawaiId, $bagian, $filters);
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
