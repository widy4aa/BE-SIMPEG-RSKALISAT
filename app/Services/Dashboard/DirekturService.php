<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\DirekturDashboardRepository;

class DirekturService
{
    public function __construct(private readonly DirekturDashboardRepository $repository)
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
