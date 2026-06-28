<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use InvalidArgumentException;

class HrdDiklatMasterService
{
    public function __construct(private readonly PegawaiDiklatRepository $pegawaiDiklatRepository) {}

    public function createMasterDiklat(int $userId, array $payload): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $jenisPelaksana = strtolower((string) ($payload['jenis_pelaksana'] ?? ''));
        $isInternal = $jenisPelaksana === 'internal';

        if ($jenisPelaksana !== 'internal') {
            throw new InvalidArgumentException('Master Diklat yang dibuat HRD wajib berjenis internal.');
        }

        $tanggalMulai = Carbon::parse((string) $payload['tanggal_mulai'])->startOfDay();
        $tanggalSelesai = Carbon::parse((string) $payload['tanggal_selesai'])->startOfDay();

        $kategori = $this->pegawaiDiklatRepository->firstOrCreateKategoriByNama((string) $payload['kategori']);
        $jenisDiklat = $this->pegawaiDiklatRepository->firstOrCreateJenisByNama((string) $payload['jenis_diklat']);

        $jenisBiayaId = null;
        $totalBiaya = null;

        if ($isInternal) {
            $jenisBiayaNama = trim((string) ($payload['jenis_biaya'] ?? ''));
            if ($jenisBiayaNama === '') {
                throw new InvalidArgumentException('Jenis biaya wajib diisi untuk jenis pelaksana internal.');
            }

            if (! array_key_exists('total_biaya', $payload) || $payload['total_biaya'] === null || $payload['total_biaya'] === '') {
                throw new InvalidArgumentException('Total biaya wajib diisi untuk jenis pelaksana internal.');
            }

            $jenisBiaya = $this->pegawaiDiklatRepository->firstOrCreateJenisBiayaByNama($jenisBiayaNama);
            $jenisBiayaId = (int) $jenisBiaya->id;
            $totalBiaya = (float) $payload['total_biaya'];
        }

        $diklat = $this->pegawaiDiklatRepository->createDiklat([
            'jenis_diklat_id' => (int) $jenisDiklat->id,
            'kategori_diklat_id' => (int) $kategori->id,
            'created_by' => (int) $pegawai->id,
            'nama_kegiatan' => (string) $payload['nama_kegiatan'],
            'penyelenggara' => (string) $payload['penyelenggara'],
            'tanggal_mulai' => $tanggalMulai->toDateString(),
            'tanggal_selesai' => $tanggalSelesai->toDateString(),
            'tempat' => (string) $payload['lokasi'],
            'waktu' => $payload['waktu'] ?? null,
            'jp' => (int) $payload['jp'],
            'total_biaya' => $totalBiaya,
            'jenis_biaya_id' => $jenisBiayaId,
            'jenis_pelaksanaan' => $jenisPelaksana,
            'catatan' => (string) ($payload['catatan'] ?? ''),
        ]);

        return [
            'id_diklat' => (int) $diklat->id,
            'nama_kegiatan' => (string) $diklat->nama_kegiatan,
            'kategori' => (string) $kategori->nama,
            'jenis_diklat' => (string) $jenisDiklat->nama,
            'penyelenggara' => (string) $diklat->penyelenggara,
            'lokasi' => (string) ($diklat->tempat ?? ''),
            'tanggal_mulai' => optional($diklat->tanggal_mulai)?->toDateString(),
            'tanggal_selesai' => optional($diklat->tanggal_selesai)?->toDateString(),
            'waktu' => optional($diklat->waktu)?->format('H:i:s'),
            'jp' => $diklat->jp,
            'jenis_biaya' => $isInternal ? (string) ($payload['jenis_biaya'] ?? '') : null,
            'total_biaya' => $diklat->total_biaya,
            'catatan' => (string) ($diklat->catatan ?? ''),
            'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
        ];
    }

    public function updateMasterDiklat(int $diklatId, int $userId, array $payload): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $diklat = $this->pegawaiDiklatRepository->findDiklatById($diklatId);
        if ($diklat === null) {
            throw new InvalidArgumentException('Master Diklat tidak ditemukan.');
        }

        $jenisPelaksana = strtolower((string) ($payload['jenis_pelaksana'] ?? ''));
        $isInternal = $jenisPelaksana === 'internal';

        if ($jenisPelaksana !== 'internal') {
            throw new InvalidArgumentException('Master Diklat yang dibuat HRD wajib berjenis internal.');
        }

        $tanggalMulai = Carbon::parse((string) $payload['tanggal_mulai'])->startOfDay();
        $tanggalSelesai = Carbon::parse((string) $payload['tanggal_selesai'])->startOfDay();

        $kategori = $this->pegawaiDiklatRepository->firstOrCreateKategoriByNama((string) $payload['kategori']);
        $jenisDiklat = $this->pegawaiDiklatRepository->firstOrCreateJenisByNama((string) $payload['jenis_diklat']);

        $jenisBiayaId = null;
        $totalBiaya = null;

        if ($isInternal) {
            $jenisBiayaNama = trim((string) ($payload['jenis_biaya'] ?? ''));
            if ($jenisBiayaNama === '') {
                throw new InvalidArgumentException('Jenis biaya wajib diisi untuk jenis pelaksana internal.');
            }

            if (! array_key_exists('total_biaya', $payload) || $payload['total_biaya'] === null || $payload['total_biaya'] === '') {
                throw new InvalidArgumentException('Total biaya wajib diisi untuk jenis pelaksana internal.');
            }

            $jenisBiaya = $this->pegawaiDiklatRepository->firstOrCreateJenisBiayaByNama($jenisBiayaNama);
            $jenisBiayaId = (int) $jenisBiaya->id;
            $totalBiaya = (float) $payload['total_biaya'];
        }

        $diklat->jenis_diklat_id = (int) $jenisDiklat->id;
        $diklat->kategori_diklat_id = (int) $kategori->id;
        $diklat->nama_kegiatan = (string) $payload['nama_kegiatan'];
        $diklat->penyelenggara = (string) $payload['penyelenggara'];
        $diklat->tanggal_mulai = $tanggalMulai->toDateString();
        $diklat->tanggal_selesai = $tanggalSelesai->toDateString();
        $diklat->tempat = (string) $payload['lokasi'];
        $diklat->waktu = $payload['waktu'] ?? null;
        $diklat->jp = (int) $payload['jp'];
        $diklat->total_biaya = $totalBiaya;
        $diklat->jenis_biaya_id = $jenisBiayaId;
        $diklat->jenis_pelaksanaan = $jenisPelaksana;
        $diklat->catatan = (string) ($payload['catatan'] ?? '');

        $this->pegawaiDiklatRepository->saveDiklat($diklat);

        return [
            'id_diklat' => (int) $diklat->id,
            'nama_kegiatan' => (string) $diklat->nama_kegiatan,
            'kategori' => (string) $kategori->nama,
            'jenis_diklat' => (string) $jenisDiklat->nama,
            'penyelenggara' => (string) $diklat->penyelenggara,
            'lokasi' => (string) ($diklat->tempat ?? ''),
            'tanggal_mulai' => optional($diklat->tanggal_mulai)?->toDateString(),
            'tanggal_selesai' => optional($diklat->tanggal_selesai)?->toDateString(),
            'waktu' => optional($diklat->waktu)?->format('H:i:s'),
            'jp' => $diklat->jp,
            'jenis_biaya' => $isInternal ? (string) ($payload['jenis_biaya'] ?? '') : null,
            'total_biaya' => $diklat->total_biaya,
            'catatan' => (string) ($diklat->catatan ?? ''),
            'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
        ];
    }
}
