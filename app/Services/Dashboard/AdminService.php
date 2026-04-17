<?php

namespace App\Services\Dashboard;

class AdminService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Selamat datang admin',
            'summary' => [
                'label' => 'Dashboard admin',
            ],
        ];
    }
}
