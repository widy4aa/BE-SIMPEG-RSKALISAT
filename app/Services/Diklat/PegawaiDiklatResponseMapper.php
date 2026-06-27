<?php

namespace App\Services\Diklat;

class PegawaiDiklatResponseMapper
{
    public function mutation(object $diklat, object $jadwal, ?object $kategori, ?object $jenisDiklat, ?string $jenisBiaya): array
    {
        return [
            'id_diklat' => (int) $diklat->id,
            'id_jadwal_diklat' => (int) $jadwal->id,
            'nama_kegiatan' => (string) ($diklat->nama_kegiatan ?? ''),
            'kategori' => (string) ($kategori?->nama ?? ''),
            'jenis_diklat' => (string) ($jenisDiklat?->nama ?? ''),
            'penyelenggara' => (string) ($diklat->penyelenggara ?? ''),
            'lokasi' => (string) ($diklat->tempat ?? ''),
            'tanggal_mulai' => optional($diklat->tanggal_mulai)?->toDateString(),
            'tanggal_selesai' => optional($diklat->tanggal_selesai)?->toDateString(),
            'waktu' => optional($diklat->waktu)?->format('H:i:s'),
            'status_diklat' => (string) ($jadwal->status_diklat ?? ''),
            'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
            'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
            'jp' => $diklat->jp,
            'jenis_biaya' => $jenisBiaya,
            'total_biaya' => $diklat->total_biaya,
            'catatan' => (string) ($diklat->catatan ?? ''),
            'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
            'status_kelayakan' => $jadwal->status_kelayakan,
            'status_validasi' => $jadwal->status_validasi,
        ];
    }

    public function deleted(object $diklat, object $jadwal): array
    {
        return [
            'id_diklat' => (int) $diklat->id,
            'id_jadwal_diklat' => (int) $jadwal->id,
            'deleted' => true,
        ];
    }

    public function laporanUploaded(object $diklat, object $jadwal): array
    {
        return [
            'id_diklat' => (int) $diklat->id,
            'id_jadwal_diklat' => (int) $jadwal->id,
            'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
            'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
            'status_validasi' => $jadwal->status_validasi,
            'uploaded_at' => $jadwal->uploaded_at?->toDateTimeString(),
        ];
    }
}
