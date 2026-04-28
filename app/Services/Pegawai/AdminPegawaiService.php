<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\AdminPegawaiRepository;
use Illuminate\Support\Facades\Storage;

class AdminPegawaiService
{
    public function __construct(
        private readonly AdminPegawaiRepository $repository
    ) {
    }

    public function getPegawaiData(): array
    {
        $pegawaiList = $this->repository->getAllPegawai();

        $totalPegawai = $pegawaiList->count();
        
        $jumlahDokter = 0;
        $jumlahPerawat = 0;
        $uniqueProfesiIds = [];
        
        $mappedData = [];

        foreach ($pegawaiList as $pegawai) {
            $profesiNama = strtolower($pegawai->profesi?->nama ?? '');
            
            if (str_contains($profesiNama, 'dokter')) {
                $jumlahDokter++;
            }
            if (str_contains($profesiNama, 'perawat')) {
                $jumlahPerawat++;
            }

            if ($pegawai->profesi_id) {
                $uniqueProfesiIds[$pegawai->profesi_id] = true;
            }

            $fotoPath = $pegawai->pribadi?->foto_path;
            $linkPhotoProfil = $fotoPath ? url(Storage::url($fotoPath)) : null;

            $mappedData[] = [
                'id_pegawai' => $pegawai->id,
                'nama' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'link_photo_profil' => $linkPhotoProfil,
                'jabatan' => $pegawai->jabatan?->nama,
                'unit_kerja' => $pegawai->jabatan?->unitKerja?->nama,
                'email' => $pegawai->user?->email,
                'no_telp' => $pegawai->pribadi?->no_hp ?? $pegawai->pribadi?->no_telp,
                'status' => $pegawai->status_pegawai,
            ];
        }

        return [
            'total_pegawai' => $totalPegawai,
            'jumlah_dokter' => $jumlahDokter,
            'jumlah_perawat' => $jumlahPerawat,
            'jumlah_profesi' => count($uniqueProfesiIds),
            'pegawai' => $mappedData,
        ];
    }
}
