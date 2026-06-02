<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\HrdDashboardRepository;

class DirekturService
{
    public function __construct(private readonly HrdDashboardRepository $repository)
    {
    }

    public function build(int $userId, ?string $type = null): array
    {
        $stats = $this->repository->getDashboardStats($type);

        return [
            'welcome' => 'Selamat datang direktur',
            'summary' => array_merge([
                'label' => 'Dashboard direktur',
            ], $stats),
        ];
    }
}
