<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PegawaiService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
    ) {
    }

    public function build(int $userId, array $filters = []): array
    {
        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        $perPage = $this->resolvePerPage($filters['per_page'] ?? null, 7);

        if ($pegawai === null) {
            $totalRiwayat = 0;
            $selesai = 0;
            $akanDatang = 0;
            $paginatedRiwayat = \App\Models\ListJadwalDiklat::query()->whereRaw('1 = 0')->paginate($perPage);
        } else {
            $riwayatQuery = \App\Models\ListJadwalDiklat::query()->where('pegawai_id', $pegawai->id);
            $totalRiwayat = $riwayatQuery->count();
            $selesai = (clone $riwayatQuery)->where('status_diklat', 'sudah terlaksana')->count();
            $akanDatang = (clone $riwayatQuery)->where('status_diklat', 'belum terlaksana')->count();

            $paginatedRiwayat = $this->pegawaiDiklatRepository->getPaginatedRiwayatDiklatByPegawaiId((int) $pegawai->id, $perPage, $filters);
        }

        $mappedData = $paginatedRiwayat->getCollection()->map(function ($jadwal): array {
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

        $paginatedRiwayat->setCollection($mappedData);

        return [
            'welcome' => 'Daftar diklat pegawai berhasil diambil.',
            'summary' => [
                'label' => 'Diklat pegawai',
                'ringkasan' => [
                    'total_riwayat' => $totalRiwayat,
                    'selesai' => $selesai,
                    'akan_datang' => $akanDatang,
                ],
                'riwayat_diklat' => $paginatedRiwayat,
                'catatan' => 'Data diklat diambil dari database untuk role pegawai.',
            ],
        ];
    }

    public function create(int $userId, array $payload, ?UploadedFile $sertifFile = null): array
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
        $statusKelayakan = null;
        $statusValidasi = null;

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
            $statusKelayakan = 'layak';
            $statusValidasi = null;
        } else {
            $statusValidasi = 'valid';
        }

        $sertifPath = null;
        if ($sertifFile !== null) {
            $folder = public_path('dokumen/sertif-diklat');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sertif-%d-%d.%s',
                (int) $pegawai->id,
                time(),
                $sertifFile->getClientOriginalExtension()
            );

            $sertifFile->move($folder, $filename);
            $sertifPath = 'dokumen/sertif-diklat/'.$filename;
        }

        $statusDiklat = $this->resolveStatusDiklatByTanggal($tanggalMulai, $tanggalSelesai);

        [$diklat, $jadwal] = DB::transaction(function () use (
            $pegawai,
            $kategori,
            $jenisDiklat,
            $payload,
            $tanggalMulai,
            $tanggalSelesai,
            $jenisPelaksana,
            $jenisBiayaId,
            $totalBiaya,
            $statusKelayakan,
            $statusValidasi,
            $sertifPath,
            $statusDiklat
        ) {
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

            $jadwal = $this->pegawaiDiklatRepository->createJadwalDiklat([
                'diklat_id' => (int) $diklat->id,
                'pegawai_id' => (int) $pegawai->id,
                'sertif_file_path' => $sertifPath,
                'no_sertif' => (string) ($payload['no_sertif'] ?? ''),
                'uploaded_at' => $sertifPath ? now() : null,
                'status_diklat' => $statusDiklat,
                'status_kelayakan' => $statusKelayakan,
                'status_validasi' => $statusValidasi,
            ]);

            return [$diklat, $jadwal];
        });

        return [
            'id_diklat' => (int) $diklat->id,
            'id_jadwal_diklat' => (int) $jadwal->id,
            'nama_kegiatan' => (string) $diklat->nama_kegiatan,
            'kategori' => (string) $kategori->nama,
            'jenis_diklat' => (string) $jenisDiklat->nama,
            'penyelenggara' => (string) $diklat->penyelenggara,
            'lokasi' => (string) ($diklat->tempat ?? ''),
            'tanggal_mulai' => optional($diklat->tanggal_mulai)?->toDateString(),
            'tanggal_selesai' => optional($diklat->tanggal_selesai)?->toDateString(),
            'waktu' => optional($diklat->waktu)?->format('H:i:s'),
            'status_diklat' => (string) $jadwal->status_diklat,
            'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
            'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
            'jp' => $diklat->jp,
            'jenis_biaya' => $isInternal ? (string) ($payload['jenis_biaya'] ?? '') : null,
            'total_biaya' => $diklat->total_biaya,
            'catatan' => (string) ($diklat->catatan ?? ''),
            'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
            'status_kelayakan' => $jadwal->status_kelayakan,
            'status_validasi' => $jadwal->status_validasi,
        ];
    }

    public function update(int $diklatId, int $userId, array $payload, ?UploadedFile $sertifFile = null): array
    {
        if ($diklatId <= 0) {
            throw new InvalidArgumentException('ID diklat tidak valid.');
        }

        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $jadwal = $this->pegawaiDiklatRepository->findJadwalByDiklatIdAndPegawaiId($diklatId, (int) $pegawai->id);
        if ($jadwal === null || $jadwal->diklat === null) {
            throw new InvalidArgumentException('Data diklat tidak ditemukan atau bukan milik pegawai login.');
        }

        $diklat = $jadwal->diklat;
        $isOwner = (int) $diklat->created_by === (int) $pegawai->id;
        $jenisPelaksanaCurrent = (string) ($diklat->jenis_pelaksanaan ?? '');

        if ($isOwner && array_key_exists('jenis_pelaksana', $payload)) {
            $jenisPelaksanaNew = strtolower((string) ($payload['jenis_pelaksana'] ?? ''));
            if ($jenisPelaksanaNew !== '' && $jenisPelaksanaNew !== $jenisPelaksanaCurrent) {
                throw new InvalidArgumentException('Jenis pelaksana internal/external tidak bisa diubah.');
            }
        }

        if ($jenisPelaksanaCurrent === 'internal' && (string) ($jadwal->status_validasi ?? '') === 'valid') {
            throw new InvalidArgumentException('Diklat internal yang sudah valid tidak bisa diedit.');
        }

        if ($jenisPelaksanaCurrent === 'external' && (string) ($jadwal->status_kelayakan ?? '') === 'layak') {
            throw new InvalidArgumentException('Diklat eksternal yang sudah layak tidak bisa diedit.');
        }

        $kategori = $diklat->kategoriDiklat;
        $jenisDiklat = $diklat->jenisDiklat;

        if ($isOwner) {
            $tanggalMulai = array_key_exists('tanggal_mulai', $payload)
                ? Carbon::parse((string) $payload['tanggal_mulai'])->startOfDay()
                : optional($diklat->tanggal_mulai)?->copy()?->startOfDay();

            $tanggalSelesai = array_key_exists('tanggal_selesai', $payload)
                ? Carbon::parse((string) $payload['tanggal_selesai'])->startOfDay()
                : optional($diklat->tanggal_selesai)?->copy()?->startOfDay();

            if ($tanggalMulai === null || $tanggalSelesai === null) {
                throw new InvalidArgumentException('Tanggal mulai dan tanggal selesai harus tersedia.');
            }

            if (array_key_exists('kategori', $payload) && trim((string) $payload['kategori']) !== '') {
                $kategori = $this->pegawaiDiklatRepository->firstOrCreateKategoriByNama((string) $payload['kategori']);
                $diklat->kategori_diklat_id = (int) $kategori->id;
            }

            if (array_key_exists('jenis_diklat', $payload) && trim((string) $payload['jenis_diklat']) !== '') {
                $jenisDiklat = $this->pegawaiDiklatRepository->firstOrCreateJenisByNama((string) $payload['jenis_diklat']);
                $diklat->jenis_diklat_id = (int) $jenisDiklat->id;
            }

            if (array_key_exists('nama_kegiatan', $payload)) {
                $diklat->nama_kegiatan = (string) ($payload['nama_kegiatan'] ?? '');
            }

            if (array_key_exists('penyelenggara', $payload)) {
                $diklat->penyelenggara = (string) ($payload['penyelenggara'] ?? '');
            }

            if (array_key_exists('lokasi', $payload)) {
                $diklat->tempat = (string) ($payload['lokasi'] ?? '');
            }

            if (array_key_exists('jp', $payload) && $payload['jp'] !== null && $payload['jp'] !== '') {
                $diklat->jp = (int) $payload['jp'];
            }

            if (array_key_exists('catatan', $payload)) {
                $diklat->catatan = (string) ($payload['catatan'] ?? '');
            }

            if (array_key_exists('waktu', $payload)) {
                $diklat->waktu = $payload['waktu'];
            }

            $diklat->tanggal_mulai = $tanggalMulai->toDateString();
            $diklat->tanggal_selesai = $tanggalSelesai->toDateString();

            if ($jenisPelaksanaCurrent === 'internal') {
                if (array_key_exists('jenis_biaya', $payload) && trim((string) ($payload['jenis_biaya'] ?? '')) !== '') {
                    $jenisBiaya = $this->pegawaiDiklatRepository->firstOrCreateJenisBiayaByNama((string) $payload['jenis_biaya']);
                    $diklat->jenis_biaya_id = (int) $jenisBiaya->id;
                }

                if (array_key_exists('total_biaya', $payload) && $payload['total_biaya'] !== null && $payload['total_biaya'] !== '') {
                    $diklat->total_biaya = (float) $payload['total_biaya'];
                }
            } else {
                $diklat->jenis_biaya_id = null;
                $diklat->total_biaya = null;
            }
        }

        if ($jenisPelaksanaCurrent === 'internal') {
            // Internal selalu layak; status validasi di-reset ke null agar divalidasi ulang jika diedit
            $jadwal->status_kelayakan = 'layak';
            $jadwal->status_validasi = null;
        } else {
            // External tidak memerlukan validasi valid/tidak valid. Status kelayakan di-reset agar dievaluasi ulang.
            $jadwal->status_validasi = null;
            $jadwal->status_kelayakan = null;
        }

        if (array_key_exists('no_sertif', $payload)) {
            $jadwal->no_sertif = (string) ($payload['no_sertif'] ?? '');
        }

        if ($sertifFile !== null) {
            $folder = public_path('dokumen/sertif-diklat');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sertif-%d-%d.%s',
                (int) $pegawai->id,
                time(),
                $sertifFile->getClientOriginalExtension()
            );

            $sertifFile->move($folder, $filename);
            $jadwal->sertif_file_path = 'dokumen/sertif-diklat/'.$filename;
            $jadwal->uploaded_at = now();
        }

        $jadwal->status_diklat = $this->resolveStatusDiklatByTanggal(
            Carbon::parse($diklat->tanggal_mulai)->startOfDay(),
            Carbon::parse($diklat->tanggal_selesai)->startOfDay()
        );

        DB::transaction(function () use ($diklat, $jadwal, $isOwner): void {
            if ($isOwner) {
                $this->pegawaiDiklatRepository->saveDiklat($diklat);
            }
            $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);
        });

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
            'jenis_biaya' => (string) ($diklat->jenisBiaya?->nama ?? ''),
            'total_biaya' => $diklat->total_biaya,
            'catatan' => (string) ($diklat->catatan ?? ''),
            'jenis_pelaksana' => (string) ($diklat->jenis_pelaksanaan ?? ''),
            'status_kelayakan' => $jadwal->status_kelayakan,
            'status_validasi' => $jadwal->status_validasi,
        ];
    }

    public function delete(int $diklatId, int $userId): array
    {
        if ($diklatId <= 0) {
            throw new InvalidArgumentException('ID diklat tidak valid.');
        }

        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $jadwal = $this->pegawaiDiklatRepository->findJadwalByDiklatIdAndPegawaiId($diklatId, (int) $pegawai->id);
        if ($jadwal === null || $jadwal->diklat === null) {
            throw new InvalidArgumentException('Data diklat tidak ditemukan atau bukan milik pegawai login.');
        }

        $diklat = $jadwal->diklat;

        if ((int) $diklat->created_by !== (int) $pegawai->id) {
            throw new InvalidArgumentException('Anda tidak berhak menghapus diklat yang bukan buatan Anda.');
        }

        $statusKelayakan = strtolower((string) ($jadwal->status_kelayakan ?? ''));
        $statusValidasi = strtolower((string) ($jadwal->status_validasi ?? ''));

        if ($statusKelayakan === 'layak' || $statusValidasi === 'valid') {
            throw new InvalidArgumentException('Diklat tidak bisa dihapus karena sudah masuk kelayakan atau sudah validasi.');
        }

        DB::transaction(function () use ($jadwal, $diklat): void {
            $this->pegawaiDiklatRepository->deleteJadwalDiklat($jadwal);

            $remaining = $this->pegawaiDiklatRepository->countRemainingJadwalByDiklatId((int) $diklat->id);
            if ($remaining === 0) {
                $this->pegawaiDiklatRepository->deleteDiklat($diklat);
            }
        });

        return [
            'id_diklat' => (int) $diklat->id,
            'id_jadwal_diklat' => (int) $jadwal->id,
            'deleted' => true,
        ];
    }

    public function uploadLaporan(int $diklatId, int $userId, array $payload, ?UploadedFile $laporanFile = null): array
    {
        if ($diklatId <= 0) {
            throw new InvalidArgumentException('ID diklat tidak valid.');
        }

        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pegawaiDiklatRepository->findPegawaiByUserId($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $jadwal = $this->pegawaiDiklatRepository->findJadwalByDiklatIdAndPegawaiId($diklatId, (int) $pegawai->id);
        if ($jadwal === null || $jadwal->diklat === null) {
            throw new InvalidArgumentException('Data diklat tidak ditemukan atau bukan milik pegawai login.');
        }

        if ((string) ($jadwal->status_validasi ?? '') === 'valid') {
            throw new InvalidArgumentException('Laporan tidak bisa diupload/diedit karena status validasi sudah valid.');
        }

        if (array_key_exists('no_sertif', $payload)) {
            $jadwal->no_sertif = (string) ($payload['no_sertif'] ?? '');
        }

        if ($laporanFile !== null) {
            $folder = public_path('dokumen/sertif-diklat');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sertif-%d-%d.%s',
                (int) $pegawai->id,
                time(),
                $laporanFile->getClientOriginalExtension()
            );

            $laporanFile->move($folder, $filename);
            $jadwal->sertif_file_path = 'dokumen/sertif-diklat/'.$filename;
            $jadwal->uploaded_at = now();
        }

        $diklat = $jadwal->diklat;
        $jenisPelaksanaCurrent = (string) ($diklat->jenis_pelaksanaan ?? '');

        if ($jenisPelaksanaCurrent === 'internal') {
            // Status kelayakan tidak diubah, status validasi direset ke null agar divalidasi ulang jika diedit/diupload laporan baru
            $jadwal->status_validasi = null;
        }

        $this->pegawaiDiklatRepository->saveJadwalDiklat($jadwal);

        return [
            'id_diklat' => (int) $diklat->id,
            'id_jadwal_diklat' => (int) $jadwal->id,
            'no_sertif' => (string) ($jadwal->no_sertif ?? ''),
            'sertif_file_path' => (string) ($jadwal->sertif_file_path ?? ''),
            'status_validasi' => $jadwal->status_validasi,
            'uploaded_at' => $jadwal->uploaded_at?->toDateTimeString(),
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

    private function shouldUploadLaporan(?string $jenisPelaksana, ?string $sertifFilePath, ?string $noSertif, ?string $statusValidasi): bool
    {
        $jenisPelaksana = strtolower(trim((string) $jenisPelaksana));
        $statusValidasi = strtolower(trim((string) $statusValidasi));
        $hasMissingLaporan = trim((string) $sertifFilePath) === '' || trim((string) $noSertif) === '';

        if ($jenisPelaksana === 'external') {
            return $hasMissingLaporan;
        }

        if ($jenisPelaksana === 'internal') {
            return $hasMissingLaporan || in_array($statusValidasi, ['', 'tidak valid', 'di tolak', 'pending'], true);
        }

        return $hasMissingLaporan;
    }
}
