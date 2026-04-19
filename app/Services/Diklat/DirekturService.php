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
                        'kategori' => 'Struktural',
                        'jenis' => 'ASN',
                        'pelaksana' => 'Pusdiklat Kemenkes',
                        'tanggal_mulai' => '2026-10-05',
                        'tanggal_selesai' => '2026-10-12',
                        'tempat' => 'Bandung',
                        'waktu' => '08:00:00',
                        'created_by' => 'HRD SIMPEG',
                        'jp' => 56,
                        'total_biaya' => 55000000,
                        'jenis_biaya' => 'APBD',
                        'jenis_pelaksana' => 'external',
                        'catatan' => 'Program prioritas peningkatan kompetensi kepemimpinan struktural.',
                    ],
                    [
                        'id' => 402,
                        'nama' => 'Pelatihan Manajemen Risiko Klinis',
                        'kategori' => 'Akred',
                        'jenis' => 'Tenkes',
                        'pelaksana' => 'Komite Mutu Nasional',
                        'tanggal_mulai' => '2026-11-03',
                        'tanggal_selesai' => '2026-11-05',
                        'tempat' => 'Jakarta',
                        'waktu' => '09:00:00',
                        'created_by' => 'Admin SIMPEG',
                        'jp' => 24,
                        'total_biaya' => 35000000,
                        'jenis_biaya' => 'Hibah',
                        'jenis_pelaksana' => 'external',
                        'catatan' => 'Penguatan mitigasi risiko klinis untuk mendukung akreditasi.',
                    ],
                ],
                'catatan' => 'Data diklat masih dummy untuk role direktur.',
            ],
        ];
    }
}
