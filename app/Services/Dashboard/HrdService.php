<?php

namespace App\Services\Dashboard;

class HrdService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Selamat datang hrd',
            'summary' => [
                'label' => 'Dashboard hrd',
            ],
        ];
    }
}
