<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;

class LaporanDiklatService
{
    public function __construct(private readonly PegawaiDiklatRepository $pegawaiDiklatRepository) {}

    public function generate(int $bulanAwal, int $tahunAwal, int $bulanAkhir, int $tahunAkhir): array
    {
        $startDate = Carbon::create($tahunAwal, $bulanAwal, 1)->startOfMonth();
        $endDate = Carbon::create($tahunAkhir, $bulanAkhir, 1)->endOfMonth();

        $diklatList = $this->pegawaiDiklatRepository->getRekapLaporanDiklatInternal($startDate, $endDate);

        $totalBiayaKeseluruhan = 0;
        $totalPegawai = 0;
        $totalBiayaPegawai = 0;
        $listPegawai = [];

        $items = $diklatList->map(function ($diklat) use (&$totalBiayaKeseluruhan, &$totalPegawai, &$totalBiayaPegawai, &$listPegawai) {
            $totalBiayaPerPeserta = (float) $diklat->total_biaya;
            $totalPesertaValidasi = (int) $diklat->total_peserta_validasi;

            $totalBiayaDiklat = $totalBiayaPerPeserta * $totalPesertaValidasi;
            $totalBiayaKeseluruhan += $totalBiayaDiklat;

            foreach ($diklat->jadwalPeserta as $jadwal) {
                $pegawai = $jadwal->pegawai;
                if ($pegawai) {
                    $totalPegawai++;
                    $totalBiayaPegawai += $totalBiayaPerPeserta;
                    $listPegawai[] = [
                        'Nama Orang' => (string) $pegawai->nama,
                        'NIK' => (string) $pegawai->nik,
                        'NIP' => (string) $pegawai->nip,
                        'unit kerja' => (string) ($pegawai->jabatan?->unitKerja?->nama ?? ''),
                        'nama_kegiatan' => (string) $diklat->nama_kegiatan,
                        'jenis_diklat' => (string) ($diklat->jenisDiklat?->nama ?? ''),
                        'penyelenggara' => (string) $diklat->penyelenggara,
                        'tanggal_mulai' => optional($diklat->tanggal_mulai)?->toDateString(),
                        'tanggal_selesai' => optional($diklat->tanggal_selesai)?->toDateString(),
                        'waktu' => optional($diklat->waktu)?->format('H:i:s'),
                        'jp' => (int) $diklat->jp,
                        'jenis_biaya' => (string) ($diklat->jenisBiaya?->nama ?? ''),
                        'biaya' => $totalBiayaPerPeserta,
                    ];
                }
            }

            return [
                'nama_kegiatan' => (string) $diklat->nama_kegiatan,
                'jenis_diklat' => (string) ($diklat->jenisDiklat?->nama ?? ''),
                'penyelenggara' => (string) $diklat->penyelenggara,
                'tanggal_mulai' => optional($diklat->tanggal_mulai)?->toDateString(),
                'tanggal_selesai' => optional($diklat->tanggal_selesai)?->toDateString(),
                'waktu' => optional($diklat->waktu)?->format('H:i:s'),
                'JP' => (int) $diklat->jp,
                'jenis_biaya' => (string) ($diklat->jenisBiaya?->nama ?? ''),
                'Total Peserta' => (int) $diklat->total_peserta,
                'Total Peserta yang udah Validasi' => $totalPesertaValidasi,
                'total_biaya per peserta' => $totalBiayaPerPeserta,
                'Total Biaya Diklat' => $totalBiayaDiklat,
            ];
        })->values()->all();

        return [
            'periode_awal' => $startDate->format('Y-m'),
            'periode_akhir' => $endDate->format('Y-m'),
            'waktu_generate' => Carbon::now()->format('Y-m-d H:i:s'),
            'Rekap Diklat' => [
                'total' => count($items),
                'total_biaya keseluruhan' => $totalBiayaKeseluruhan,
                'list_diklat' => $items,
            ],
            'rekap_pegawai' => [
                'total pegawai' => $totalPegawai,
                'total biaya' => $totalBiayaPegawai,
                'list_pegawai' => $listPegawai,
            ],
        ];
    }
}
