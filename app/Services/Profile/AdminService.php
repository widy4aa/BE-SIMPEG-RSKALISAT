<?php

namespace App\Services\Profile;

class AdminService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Selamat datang admin',
            'summary' => [
                'label' => 'Profile admin',
                'nama' => 'Admin Dummy',
                'email' => 'admin.dummy@example.com',
                'no_telp' => '081200000001',
                'unit_kerja' => 'SDM',
                'catatan' => 'Data profile masih dummy.',
            ],
        ];
    }
}
