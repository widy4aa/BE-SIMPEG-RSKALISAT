<?php

namespace App\Services\Pegawai;

class HrdPegawaiService
{
    public function getPegawaiData(): array
    {
        return [
            'total_pegawai' => 0,
            'jumlah_dokter' => 0,
            'jumlah_perawat' => 0,
            'jumlah_profesi' => 0,
            'pegawai' => [],
            'note' => 'Dummy data for HRD'
        ];
    }
}
