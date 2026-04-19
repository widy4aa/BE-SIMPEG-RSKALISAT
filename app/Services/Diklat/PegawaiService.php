<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;

class PegawaiService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
    ) {
    }

    public function build(int $userId): array
    {
        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);

        $riwayatDiklat = $pegawai === null
            ? collect()
            : $this->pegawaiDiklatRepository->getRiwayatDiklatByPegawaiId((int) $pegawai->id);

        $riwayat = $riwayatDiklat->map(function ($jadwal): array {
            $diklat = $jadwal->diklat;
            $tanggalMulai = $diklat?->tanggal_mulai;
            $tanggalSelesai = $diklat?->tanggal_selesai;

            return [
                'id' => (int) ($diklat?->id ?? $jadwal->id),
                'nama' => (string) ($diklat?->nama_kegiatan ?? ''),
                'kategori' => (string) ($diklat?->kategoriDiklat?->nama ?? ''),
                'jenis' => (string) ($diklat?->jenisDiklat?->nama ?? ''),
                'pelaksana' => (string) ($diklat?->penyelenggara ?? ''),
                'tanggal_mulai' => optional($tanggalMulai)?->toDateString(),
                'tanggal_selesai' => optional($tanggalSelesai)?->toDateString(),
                'status' => $this->resolveStatusByTanggal($tanggalMulai, $tanggalSelesai),
                'tempat' => (string) ($diklat?->tempat ?? ''),
                'waktu' => optional($diklat?->waktu)?->format('H:i:s'),
                'created_by' => (string) ($diklat?->createdByPegawai?->nama ?? ''),
                'jp' => $diklat?->jp,
                'total_biaya' => $diklat?->total_biaya,
                'jenis_biaya' => (string) ($diklat?->jenisBiaya?->nama ?? ''),
                'jenis_pelaksana' => (string) ($diklat?->jenis_pelaksanaan ?? ''),
            ];
        })->values()->all();

        return [
            'welcome' => 'Daftar diklat pegawai berhasil diambil.',
            'summary' => [
                'label' => 'Diklat pegawai',
                'ringkasan' => [
                    'total_riwayat' => $riwayatDiklat->count(),
                    'selesai' => $riwayatDiklat->where('status_diklat', 'sudah terlaksana')->count(),
                    'akan_datang' => $riwayatDiklat->where('status_diklat', 'belum terlaksana')->count(),
                ],
                'riwayat_diklat' => $riwayat,
                'catatan' => 'Data diklat diambil dari database untuk role pegawai.',
            ],
        ];
    }

    private function resolveStatusByTanggal(mixed $tanggalMulai, mixed $tanggalSelesai): string
    {
        $today = Carbon::today();

        $mulai = $tanggalMulai instanceof Carbon
            ? $tanggalMulai->copy()->startOfDay()
            : ($tanggalMulai ? Carbon::parse($tanggalMulai)->startOfDay() : null);

        $selesai = $tanggalSelesai instanceof Carbon
            ? $tanggalSelesai->copy()->startOfDay()
            : ($tanggalSelesai ? Carbon::parse($tanggalSelesai)->startOfDay() : null);

        if ($mulai !== null && $today->lt($mulai)) {
            return 'mendatang';
        }

        if ($selesai !== null && $today->gt($selesai)) {
            return 'selesai';
        }

        return 'berlangsung';
    }
}
