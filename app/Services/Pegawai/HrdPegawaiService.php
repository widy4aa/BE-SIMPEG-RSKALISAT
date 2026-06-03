<?php

namespace App\Services\Pegawai;

use App\Repositories\Pegawai\AdminPegawaiRepository;
use Illuminate\Support\Facades\Storage;

class HrdPegawaiService
{
    public function __construct(
        private readonly AdminPegawaiRepository $repository
    ) {
    }

    public function getPegawaiData(): array
    {
        $totalPegawai = \App\Models\Pegawai::count();
        $jumlahDokter = \App\Models\Pegawai::whereHas('profesi', function($q) {
            $q->where('nama', 'like', '%dokter%');
        })->count();
        $jumlahPerawat = \App\Models\Pegawai::whereHas('profesi', function($q) {
            $q->where('nama', 'like', '%perawat%');
        })->count();
        $jumlahProfesi = \Illuminate\Support\Facades\DB::table('pegawai')->whereNotNull('profesi_id')->distinct('profesi_id')->count('profesi_id');

        $paginatedPegawai = $this->repository->getPaginatedPegawai(10);
        
        $mappedData = $paginatedPegawai->getCollection()->map(function ($pegawai) {
            $fotoPath = $pegawai->pribadi?->foto_path;
            $linkPhotoProfil = $fotoPath ? url(Storage::url($fotoPath)) : null;

            return [
                'id_pegawai' => $pegawai->id,
                'nama' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'link_photo_profil' => $linkPhotoProfil,
                'jabatan' => $pegawai->jabatan?->nama,
                'unit_kerja' => $pegawai->jabatan?->unitKerja?->nama,
                'email' => $pegawai->pribadi?->email,
                'no_telp' => $pegawai->pribadi?->no_hp ?? $pegawai->pribadi?->no_telp,
                'status' => $pegawai->status_pegawai,
            ];
        });

        $paginatedPegawai->setCollection($mappedData);

        return [
            'total_pegawai' => $totalPegawai,
            'jumlah_dokter' => $jumlahDokter,
            'jumlah_perawat' => $jumlahPerawat,
            'jumlah_profesi' => $jumlahProfesi,
            'pegawai' => $paginatedPegawai,
        ];
    }
}
