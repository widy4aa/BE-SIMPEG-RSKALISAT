<?php

namespace App\Services\Diklat;

class DirekturService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Ringkasan diklat direktur berhasil diambil.',
            'summary' => [
                'label' => 'Diklat direktur',
                'ringkasan' => [
                    'total_anggaran' => 250000000,
                    'sudah_terpakai' => 130000000,
                    'sisa_anggaran' => 120000000,
                    'program_prioritas' => 4,
                ],
                'keputusan_terbaru' => [
                    [
                        'id' => 401,
                        'nama' => 'Diklat Kepemimpinan Kepala Ruangan',
                        'nilai_usulan' => 55000000,
                        'status' => 'menunggu_persetujuan',
                    ],
                    [
                        'id' => 402,
                        'nama' => 'Pelatihan Manajemen Risiko Klinis',
                        'nilai_usulan' => 35000000,
                        'status' => 'disetujui',
                    ],
                ],
                'catatan' => 'Data diklat masih dummy untuk role direktur.',
            ],
        ];
    }
}
