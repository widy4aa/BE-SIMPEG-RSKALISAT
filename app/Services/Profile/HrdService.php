<?php

namespace App\Services\Profile;

class HrdService
{
    public function __construct(
        private readonly PegawaiService $pegawaiService,
    ) {
    }

    public function build(int $userId): array
    {
        return $this->pegawaiService->buildForRole($userId, 'hrd');
    }
}
