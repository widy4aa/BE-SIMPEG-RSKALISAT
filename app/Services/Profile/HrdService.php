<?php

namespace App\Services\Profile;

class HrdService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Selamat datang hrd',
            'summary' => [
                'label' => 'Profile hrd',
                'nama' => 'HRD Dummy',
                'email' => 'hrd.dummy@example.com',
                'no_telp' => '081200000002',
                'jabatan' => 'Staf HRD',
                'unit_kerja' => 'SDM',
                'catatan' => 'Data profile masih dummy.',
            ],
        ];
    }
}
