<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HrdService
{
    public function __construct(private readonly PegawaiDiklatRepository $pegawaiDiklatRepository)
    {
    }

    public function build(int $userId): array
    {
        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);

        if ($pegawai === null) {
            $totalRiwayat = 0;
            $selesai = 0;
            $akanDatang = 0;
            $paginatedRiwayat = \App\Models\ListJadwalDiklat::query()->whereRaw('1 = 0')->paginate(7);
        } else {
            $riwayatQuery = \App\Models\ListJadwalDiklat::query()->where('pegawai_id', $pegawai->id);
            $totalRiwayat = $riwayatQuery->count();
            $selesai = (clone $riwayatQuery)->where('status_diklat', 'sudah terlaksana')->count();
            $akanDatang = (clone $riwayatQuery)->where('status_diklat', 'belum terlaksana')->count();

            $paginatedRiwayat = $this->pegawaiDiklatRepository->getPaginatedRiwayatDiklatByPegawaiId((int) $pegawai->id, 7);
        }

        $riwayat = $paginatedRiwayat->getCollection()->map(function ($jadwal): array {
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
                'catatan' => (string) ($diklat?->catatan ?? ''),
                'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
                'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
                'status_validasi' => $this->resolveStatusValidasiText(
                    $diklat?->jenis_pelaksanaan,
                    $jadwal->sertif_file_path,
                    $jadwal->status_validasi
                ),
            ];
        });

        $paginatedRiwayat->setCollection($riwayat);

        return [
            'welcome' => 'Daftar diklat untuk HRD berhasil diambil.',
            'summary' => [
                'label' => 'Diklat hrd',
                'ringkasan' => [
                    'total_riwayat' => $totalRiwayat,
                    'selesai' => $selesai,
                    'akan_datang' => $akanDatang,
                ],
                'riwayat_diklat' => $paginatedRiwayat,
            ],
        ];
    }

    public function getAllDiklat(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $paginatedDiklat = $this->pegawaiDiklatRepository->getPaginatedMasterDiklat(7);

        $items = $paginatedDiklat->getCollection()->map(function ($diklat): array {
            $tanggalMulai = $diklat->tanggal_mulai;
            $tanggalSelesai = $diklat->tanggal_selesai;

            return [
                'id_diklat' => (int) $diklat->id,
                'nama' => (string) ($diklat->nama_kegiatan ?? ''),
                'kategori' => (string) ($diklat->kategoriDiklat?->nama ?? ''),
                'jenis' => (string) ($diklat->jenisDiklat?->nama ?? ''),
                'pelaksana' => (string) ($diklat->penyelenggara ?? ''),
                'tanggal_mulai' => optional($tanggalMulai)?->toDateString(),
                'tanggal_selesai' => optional($tanggalSelesai)?->toDateString(),
                'status' => $this->resolveStatusByTanggal($tanggalMulai, $tanggalSelesai),
                'tempat' => (string) ($diklat->tempat ?? ''),
                'waktu' => optional($diklat->waktu)?->format('H:i:s'),
                'created_by' => (string) ($diklat->createdByPegawai?->nama ?? ''),
                'jp' => $diklat->jp,
                'total_biaya' => $diklat->total_biaya,
                'jenis_biaya' => (string) ($diklat->jenisBiaya?->nama ?? ''),
                'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
                'catatan' => (string) ($diklat->catatan ?? ''),
                'jumlah_peserta' => $diklat->jadwal_peserta_count ?? 0,
            ];
        });

        $paginatedDiklat->setCollection($items);

        return $paginatedDiklat;
    }

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

        if (! in_array($jenisPelaksana, ['internal', 'external'], true)) {
            throw new InvalidArgumentException('Jenis pelaksana tidak valid.');
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
            'waktu' => null,
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
            'jp' => $diklat->jp,
            'jenis_biaya' => $isInternal ? (string) ($payload['jenis_biaya'] ?? '') : null,
            'total_biaya' => $diklat->total_biaya,
            'catatan' => (string) ($diklat->catatan ?? ''),
            'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
        ];
    }

    public function getPesertaDiklat(int $diklatId): array
    {
        $pesertaIds = $this->pegawaiDiklatRepository->getPesertaDiklatIds($diklatId);
        $semuaPegawai = $this->pegawaiDiklatRepository->getAllPegawaiWithUnitKerjaProfesi();

        $peserta = $semuaPegawai->map(function ($pegawai) use ($pesertaIds): array {
            return [
                'pegawai_id' => (int) $pegawai->id,
                'nama' => (string) $pegawai->nama,
                'nik' => (string) $pegawai->nik,
                'unit_kerja' => (string) ($pegawai->jabatan?->unitKerja?->nama ?? ''),
                'profesi' => (string) ($pegawai->profesi?->nama ?? ''),
                'status' => in_array($pegawai->id, $pesertaIds, true),
            ];
        })->values()->all();

        return [
            'diklat_id' => $diklatId,
            'total_pegawai' => count($peserta),
            'list' => $peserta,
        ];
    }

    public function syncPesertaDiklat(int $diklatId, array $pegawaiIds): array
    {
        $diklat = $this->pegawaiDiklatRepository->findDiklatById($diklatId);
        if ($diklat === null) {
            throw new InvalidArgumentException('Master Diklat tidak ditemukan.');
        }

        $tanggalMulai = $diklat->tanggal_mulai ? Carbon::parse($diklat->tanggal_mulai)->startOfDay() : null;
        $tanggalSelesai = $diklat->tanggal_selesai ? Carbon::parse($diklat->tanggal_selesai)->startOfDay() : null;

        $statusDiklat = null;
        if ($tanggalMulai !== null && $tanggalSelesai !== null) {
            $statusDiklat = $this->resolveStatusDiklatByTanggal($tanggalMulai, $tanggalSelesai);
        }

        DB::transaction(function () use ($diklatId, $pegawaiIds, $statusDiklat) {
            // Delete those not in the new array
            $this->pegawaiDiklatRepository->deleteJadwalNotInPegawaiIds($diklatId, $pegawaiIds);

            // Fetch existing so we don't duplicate
            $existingIds = $this->pegawaiDiklatRepository->getPesertaDiklatIds($diklatId);

            $newIds = array_diff($pegawaiIds, $existingIds);

            foreach ($newIds as $pegawaiId) {
                $this->pegawaiDiklatRepository->createJadwalDiklat([
                    'diklat_id' => $diklatId,
                    'pegawai_id' => $pegawaiId,
                    'status_diklat' => $statusDiklat,
                    'status_kelayakan' => 'layak',
                    'status_validasi' => null, // or 'valid' if needed, but standard is null until validation
                ]);
            }
        });

        return [
            'diklat_id' => $diklatId,
            'peserta_terdaftar' => count($pegawaiIds),
        ];
    }

    public function getDiklatMenungguKelayakan(): array
    {
        $jadwalList = $this->pegawaiDiklatRepository->getJadwalDiklatMenungguKelayakan();

        $items = $jadwalList->map(function ($jadwal): array {
            $diklat = $jadwal->diklat;
            $tanggalMulai = $diklat?->tanggal_mulai;
            $tanggalSelesai = $diklat?->tanggal_selesai;

            return [
                'id_diklat' => (int) ($diklat?->id ?? 0),
                'id_jadwal_diklat' => (int) $jadwal->id,
                'nama' => (string) ($diklat?->nama_kegiatan ?? ''),
                'kategori' => (string) ($diklat?->kategoriDiklat?->nama ?? ''),
                'jenis' => (string) ($diklat?->jenisDiklat?->nama ?? ''),
                'pelaksana' => (string) ($diklat?->penyelenggara ?? ''),
                'tanggal_mulai' => optional($tanggalMulai)?->toDateString(),
                'tanggal_selesai' => optional($tanggalSelesai)?->toDateString(),
                'status' => (string) ($jadwal->status_diklat ?? ''),
                'tempat' => (string) ($diklat?->tempat ?? ''),
                'waktu' => optional($diklat?->waktu)?->format('H:i:s'),
                'jp' => $diklat?->jp,
                'total_biaya' => $diklat?->total_biaya,
                'jenis_biaya' => (string) ($diklat?->jenisBiaya?->nama ?? ''),
                'jenis_pelaksana' => (string) ($diklat?->jenis_pelaksanaan ?? ''),
                'catatan' => (string) ($diklat?->catatan ?? ''),
                'pegawai_id' => (int) ($jadwal->pegawai?->id ?? 0),
                'pegawai_nama' => (string) ($jadwal->pegawai?->nama ?? ''),
                'pegawai_nik' => (string) ($jadwal->pegawai?->nik ?? ''),
                'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
                'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
                'status_kelayakan' => $jadwal->status_kelayakan,
                'status_validasi' => $jadwal->status_validasi,
            ];
        })->values()->all();

        return [
            'total' => count($items),
            'list' => $items,
        ];
    }

    public function getDiklatMenungguValidasi(): array
    {
        $jadwalList = $this->pegawaiDiklatRepository->getJadwalDiklatMenungguValidasi();

        $items = $jadwalList->map(function ($jadwal): array {
            $diklat = $jadwal->diklat;
            $tanggalMulai = $diklat?->tanggal_mulai;
            $tanggalSelesai = $diklat?->tanggal_selesai;

            return [
                'id_diklat' => (int) ($diklat?->id ?? 0),
                'id_jadwal_diklat' => (int) $jadwal->id,
                'nama' => (string) ($diklat?->nama_kegiatan ?? ''),
                'kategori' => (string) ($diklat?->kategoriDiklat?->nama ?? ''),
                'jenis' => (string) ($diklat?->jenisDiklat?->nama ?? ''),
                'pelaksana' => (string) ($diklat?->penyelenggara ?? ''),
                'tanggal_mulai' => optional($tanggalMulai)?->toDateString(),
                'tanggal_selesai' => optional($tanggalSelesai)?->toDateString(),
                'status' => (string) ($jadwal->status_diklat ?? ''),
                'tempat' => (string) ($diklat?->tempat ?? ''),
                'waktu' => optional($diklat?->waktu)?->format('H:i:s'),
                'jp' => $diklat?->jp,
                'total_biaya' => $diklat?->total_biaya,
                'jenis_biaya' => (string) ($diklat?->jenisBiaya?->nama ?? ''),
                'jenis_pelaksana' => (string) ($diklat?->jenis_pelaksanaan ?? ''),
                'catatan' => (string) ($diklat?->catatan ?? ''),
                'pegawai_id' => (int) ($jadwal->pegawai?->id ?? 0),
                'pegawai_nama' => (string) ($jadwal->pegawai?->nama ?? ''),
                'pegawai_nik' => (string) ($jadwal->pegawai?->nik ?? ''),
                'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
                'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
                'status_kelayakan' => $jadwal->status_kelayakan,
                'status_validasi' => $jadwal->status_validasi,
            ];
        })->values()->all();

        return [
            'total' => count($items),
            'list' => $items,
        ];
    }

    public function updateStatusKelayakan(int $jadwalId, bool $isLayak): array
    {
        $jadwal = $this->pegawaiDiklatRepository->findJadwalById($jadwalId);
        if ($jadwal === null) {
            throw new InvalidArgumentException('Jadwal diklat tidak ditemukan.');
        }

        $jadwal->status_kelayakan = $isLayak ? 'layak' : 'tidak layak';
        $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);

        return [
            'id_jadwal_diklat' => (int) $jadwal->id,
            'diklat_id' => (int) ($jadwal->diklat_id ?? 0),
            'pegawai_id' => (int) ($jadwal->pegawai_id ?? 0),
            'status_kelayakan' => (string) ($jadwal->status_kelayakan ?? ''),
        ];
    }

    public function updateStatusValidasi(int $jadwalId, bool $isValid): array
    {
        $jadwal = $this->pegawaiDiklatRepository->findJadwalById($jadwalId);
        if ($jadwal === null) {
            throw new InvalidArgumentException('Jadwal diklat tidak ditemukan.');
        }

        $jadwal->status_validasi = $isValid ? 'valid' : 'tidak valid';
        $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);

        return [
            'id_jadwal_diklat' => (int) $jadwal->id,
            'diklat_id' => (int) ($jadwal->diklat_id ?? 0),
            'pegawai_id' => (int) ($jadwal->pegawai_id ?? 0),
            'status_validasi' => (string) ($jadwal->status_validasi ?? ''),
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

    private function resolveStatusDiklatByTanggal(Carbon $tanggalMulai, Carbon $tanggalSelesai): string
    {
        $today = Carbon::today();

        if ($today->lt($tanggalMulai)) {
            return 'belum terlaksana';
        }

        if ($today->gt($tanggalSelesai)) {
            return 'sudah terlaksana';
        }

        return 'sedang terlaksana';
    }

    private function resolveStatusValidasiText(?string $jenisPelaksana, ?string $sertifFilePath, ?string $statusValidasi): ?string
    {
        $jenisPelaksana = strtolower((string) $jenisPelaksana);
        if ($jenisPelaksana !== 'internal') {
            return null;
        }

        if (empty($sertifFilePath)) {
            return 'Upload laporan';
        }

        if ($statusValidasi === null) {
            return 'menunggu validasi';
        }

        $statusValidasi = strtolower($statusValidasi);
        if ($statusValidasi === 'tidak valid') {
            return 'di tolak';
        }

        if ($statusValidasi === 'valid') {
            return 'diklat valid';
        }

        return null;
    }
}
