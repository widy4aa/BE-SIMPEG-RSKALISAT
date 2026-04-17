<?php

namespace App\Services\Dashboard;

class DirekturService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Selamat datang direktur',
            'summary' => [
                'label' => 'Dashboard direktur',
            ],
        ];
    }
}
