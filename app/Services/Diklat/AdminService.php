<?php

namespace App\Services\Diklat;

class AdminService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Daftar diklat untuk admin berhasil diambil.',
            'summary' => [
                'label' => 'Diklat admin',
                'ringkasan' => [
                    'total_diklat' => 24,
                    'dijadwalkan' => 10,
                    'berjalan' => 4,
                    'selesai' => 10,
                ],
                'list_diklat' => [
                    [
                        'id' => 101,
                        'nama' => 'Diklat Manajemen SDM Lanjutan',
                        'penyelenggara' => 'BPSDM Kesehatan',
                        'tanggal_mulai' => '2026-05-10',
                        'tanggal_selesai' => '2026-05-12',
                        'target_peserta' => 30,
                        'status' => 'dijadwalkan',
                    ],
                    [
                        'id' => 102,
                        'nama' => 'Workshop Audit Internal Mutu',
                        'penyelenggara' => 'Tim Mutu RS',
                        'tanggal_mulai' => '2026-04-20',
                        'tanggal_selesai' => '2026-04-21',
                        'target_peserta' => 20,
                        'status' => 'berjalan',
                    ],
                ],
                'catatan' => 'Data diklat masih dummy untuk role admin.',
            ],
        ];
    }
}
