<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HrdService
{
    public function __construct(private readonly PegawaiDiklatRepository $pegawaiDiklatRepository)
    {
    }

    public function build(int $userId, array $filters = []): array
    {
        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 7);

        if ($pegawai === null) {
            $totalRiwayat = 0;
            $selesai = 0;
            $akanDatang = 0;
            $paginatedRiwayat = new LengthAwarePaginator(collect(), 0, $perPage);
        } else {
            $summary = $this->pegawaiDiklatRepository->getJadwalSummaryByPegawaiId((int) $pegawai->id);
            $totalRiwayat = $summary['total_riwayat'];
            $selesai = $summary['selesai'];
            $akanDatang = $summary['akan_datang'];

            $paginatedRiwayat = $this->pegawaiDiklatRepository->getPaginatedRiwayatDiklatByPegawaiId((int) $pegawai->id, $perPage, $filters);
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
                'uploadlaporan' => $this->shouldUploadLaporan(
                    $diklat?->jenis_pelaksanaan,
                    $jadwal->sertif_file_path,
                    $jadwal->no_sertif,
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

    public function getAllDiklat(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 7);
        $paginatedDiklat = $this->pegawaiDiklatRepository->getPaginatedMasterDiklat($perPage, $filters);

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

        if (
            $isLayak
            && strtolower((string) ($jadwal->diklat?->jenis_pelaksanaan ?? '')) === 'external'
            && $this->hasMissingLaporan($jadwal->sertif_file_path, $jadwal->no_sertif)
        ) {
            throw new InvalidArgumentException('belum upload laporan');
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

        if (
            $isValid
            && strtolower((string) ($jadwal->diklat?->jenis_pelaksanaan ?? '')) === 'internal'
            && $this->hasMissingLaporan($jadwal->sertif_file_path, $jadwal->no_sertif)
        ) {
            throw new InvalidArgumentException('belum upload laporan');
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

    private function resolvePerPage(mixed $value, int $default): int
    {
        $perPage = is_numeric($value) ? (int) $value : $default;

        return max(1, min($perPage, 100));
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
        if (strtolower((string) $jenisPelaksana) !== 'internal') {
            return null;
        }

        if ($sertifFilePath === null || $sertifFilePath === '') {
            return 'Upload laporan';
        }

        if ($statusValidasi === null || $statusValidasi === '') {
            return 'menunggu validasi';
        }

        if ($statusValidasi === 'tidak valid') {
            return 'di tolak';
        }

        return 'diklat valid';
    }

    private function shouldUploadLaporan(?string $jenisPelaksana, ?string $sertifFilePath, ?string $noSertif, ?string $statusValidasi): bool
    {
        $jenisPelaksana = strtolower(trim((string) $jenisPelaksana));
        $statusValidasi = strtolower(trim((string) $statusValidasi));
        $hasMissingLaporan = $this->hasMissingLaporan($sertifFilePath, $noSertif);

        if ($jenisPelaksana === 'external') {
            return $hasMissingLaporan;
        }

        if ($jenisPelaksana === 'internal') {
            return $hasMissingLaporan || in_array($statusValidasi, ['', 'tidak valid', 'di tolak', 'pending'], true);
        }

        return $hasMissingLaporan;
    }

    private function hasMissingLaporan(?string $sertifFilePath, ?string $noSertif): bool
    {
        return trim((string) $sertifFilePath) === '' || trim((string) $noSertif) === '';
    }

    public function generateLaporanDiklat(int $bulanAwal, int $tahunAwal, int $bulanAkhir, int $tahunAkhir): array
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
