<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\HrdDashboardRepository;

class HrdService
{
    public function __construct(private readonly HrdDashboardRepository $repository)
    {
    }

    public function build(int $userId, ?string $type = null): array
    {
        $stats = $this->repository->getDashboardStats($type);

        return [
            'welcome' => 'Selamat datang hrd',
            'summary' => array_merge([
                'label' => 'Dashboard hrd',
            ], $stats),
        ];
    }
}
