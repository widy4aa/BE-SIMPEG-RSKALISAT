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
                        'kategori' => 'Teknis',
                        'jenis' => 'ASN',
                        'pelaksana' => 'BPSDM Kesehatan',
                        'tanggal_mulai' => '2026-05-10',
                        'tanggal_selesai' => '2026-05-12',
                        'tempat' => 'Aula RS Kalisat',
                        'waktu' => '08:00:00',
                        'created_by' => 'Admin SIMPEG',
                        'jp' => 24,
                        'total_biaya' => 5000000,
                        'jenis_biaya' => 'BLUD',
                        'jenis_pelaksana' => 'internal',
                        'catatan' => 'Program peningkatan kompetensi manajemen SDM tingkat lanjut.',
                    ],
                    [
                        'id' => 102,
                        'nama' => 'Workshop Audit Internal Mutu',
                        'kategori' => 'Akred',
                        'jenis' => 'Tenkes',
                        'pelaksana' => 'Tim Mutu RS',
                        'tanggal_mulai' => '2026-04-20',
                        'tanggal_selesai' => '2026-04-21',
                        'tempat' => 'Ruang Meeting Gedung A',
                        'waktu' => '09:00:00',
                        'created_by' => 'HRD SIMPEG',
                        'jp' => 16,
                        'total_biaya' => 3200000,
                        'jenis_biaya' => 'APBD',
                        'jenis_pelaksana' => 'external',
                        'catatan' => 'Fokus penguatan kontrol mutu dan tindak lanjut audit internal.',
                    ],
                ],
                'catatan' => 'Data diklat masih dummy untuk role admin.',
            ],
        ];
    }
}
