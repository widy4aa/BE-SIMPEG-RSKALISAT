<?php

namespace App\Services\Diklat;

class HrdService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Daftar diklat untuk HRD berhasil diambil.',
            'summary' => [
                'label' => 'Diklat hrd',
                'ringkasan' => [
                    'usulan_baru' => 8,
                    'perlu_verifikasi' => 5,
                    'disetujui_direktur' => 3,
                ],
                'list_usulan' => [
                    [
                        'id' => 301,
                        'nama' => 'Pelatihan Coding Dasar SIMRS',
                        'pengusul_unit' => 'IT',
                        'prioritas' => 'tinggi',
                        'status' => 'perlu_verifikasi',
                    ],
                    [
                        'id' => 302,
                        'nama' => 'Pelatihan Etika Pelayanan',
                        'pengusul_unit' => 'Keperawatan',
                        'prioritas' => 'sedang',
                        'status' => 'usulan_baru',
                    ],
                ],
                'catatan' => 'Data diklat masih dummy untuk role hrd.',
            ],
        ];
    }
}
