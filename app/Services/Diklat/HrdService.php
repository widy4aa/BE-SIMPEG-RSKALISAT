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
                        'kategori' => 'Teknis',
                        'jenis' => 'ASN',
                        'pelaksana' => 'Vendor SIMRS',
                        'tanggal_mulai' => '2026-08-10',
                        'tanggal_selesai' => '2026-08-12',
                        'tempat' => 'Lab Komputer RS',
                        'waktu' => '08:30:00',
                        'created_by' => 'HRD SIMPEG',
                        'jp' => 24,
                        'total_biaya' => 6500000,
                        'jenis_biaya' => 'Mandiri',
                        'jenis_pelaksana' => 'external',
                    ],
                    [
                        'id' => 302,
                        'nama' => 'Pelatihan Etika Pelayanan',
                        'kategori' => 'Fungsional',
                        'jenis' => 'Tenkes',
                        'pelaksana' => 'Tim Diklat Internal',
                        'tanggal_mulai' => '2026-09-01',
                        'tanggal_selesai' => '2026-09-01',
                        'tempat' => 'Aula Keperawatan',
                        'waktu' => '09:00:00',
                        'created_by' => 'HRD SIMPEG',
                        'jp' => 8,
                        'total_biaya' => 1500000,
                        'jenis_biaya' => 'BLUD',
                        'jenis_pelaksana' => 'internal',
                    ],
                ],
                'catatan' => 'Data diklat masih dummy untuk role hrd.',
            ],
        ];
    }
}
