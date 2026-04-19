<?php

namespace App\Services\Profile;

class DirekturService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Selamat datang direktur',
            'summary' => [
                'label' => 'Profile direktur',
                'nama' => 'Direktur Dummy',
                'email' => 'direktur.dummy@example.com',
                'no_telp' => '081200000003',
                'jabatan' => 'Direktur',
                'unit_kerja' => 'Direksi',
                'catatan' => 'Data profile masih dummy.',
            ],
        ];
    }
}
