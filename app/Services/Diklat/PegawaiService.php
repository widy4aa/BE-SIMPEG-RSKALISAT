<?php

namespace App\Services\Diklat;

class PegawaiService
{
    public function build(int $userId): array
    {
        return [
            'welcome' => 'Daftar diklat pegawai berhasil diambil.',
            'summary' => [
                'label' => 'Diklat pegawai',
                'ringkasan' => [
                    'total_riwayat' => 6,
                    'selesai' => 4,
                    'akan_datang' => 2,
                ],
                'riwayat_diklat' => [
                    [
                        'id' => 201,
                        'nama' => 'Pelatihan Komunikasi Efektif',
                        'jenis' => 'softskill',
                        'tanggal' => '2025-11-15',
                        'status' => 'selesai',
                    ],
                    [
                        'id' => 202,
                        'nama' => 'Diklat Keselamatan Pasien',
                        'jenis' => 'wajib',
                        'tanggal' => '2026-06-03',
                        'status' => 'akan_datang',
                    ],
                ],
                'catatan' => 'Data diklat masih dummy untuk role pegawai.',
            ],
        ];
    }
}
