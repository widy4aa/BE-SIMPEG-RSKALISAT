<?php

namespace App\Services\DataKeluarga;

use App\Repositories\DataKeluarga\DataKeluargaRepository;

class DataKeluargaService
{
    public function __construct(private readonly DataKeluargaRepository $repository) {}

    public function getDataKeluargaSummaryByUserId(int $userId): array
    {
        $pegawai = $this->repository->getKeluargaByUserId($userId);
        
        if (!$pegawai) {
            throw new \InvalidArgumentException('Pegawai tidak ditemukan.');
        }

        $pribadi = $pegawai->pribadi;

        $pasangan = $pribadi?->pasangan ?? collect();
        $anak = $pribadi?->anak ?? collect();
        $orangTua = $pribadi?->orangTua ?? collect();
        $kontakDarurat = $pribadi?->kontakDarurat ?? collect();
        $tanggunganLain = $pribadi?->tanggunganLain ?? collect();

        $totalKeluarga = $pasangan->count() + $anak->count() + $orangTua->count() + $kontakDarurat->count() + $tanggunganLain->count();

        return [
            'total_keluarga' => $totalKeluarga,
            'rincian' => [
                'pasangan' => $this->formatPasangan($pasangan),
                'anak' => $this->formatAnak($anak),
                'orang_tua' => $orangTua->toArray(),
                'kontak_darurat' => $kontakDarurat->toArray(),
                'tanggungan_lain' => $tanggunganLain->toArray(),
            ]
        ];
    }

    private function formatPasangan($pasangan): array
    {
        return $pasangan->map(function ($p) {
            $data = $p->toArray();
            if (!empty($p->buku_nikah_file_path)) {
                $data['link_buku_nikah'] = url('/' . $p->buku_nikah_file_path);
            } else {
                $data['link_buku_nikah'] = null;
            }
            return $data;
        })->toArray();
    }

    private function formatAnak($anak): array
    {
        return $anak->map(function ($a) {
            $data = $a->toArray();
            if (!empty($a->akta_kelahiran_file_path)) {
                $data['link_akta_kelahiran'] = url('/' . $a->akta_kelahiran_file_path);
            } else {
                $data['link_akta_kelahiran'] = null;
            }
            return $data;
        })->toArray();
    }
}
