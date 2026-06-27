<?php

namespace App\Services\Diklat;

use App\Repositories\Diklat\PegawaiDiklatRepository;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PegawaiDiklatMutationService
{
    public function __construct(
        private readonly PegawaiDiklatRepository $pegawaiDiklatRepository,
        private readonly PegawaiDiklatFileService $fileService,
        private readonly PegawaiDiklatResponseMapper $responseMapper,
    ) {}

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

        $statusPelaksanaan = $this->resolveStatusByTanggal($tanggalMulai, $tanggalSelesai);
        if ($statusPelaksanaan !== 'selesai') {
            throw new InvalidArgumentException('Pegawai hanya dapat menambahkan riwayat diklat mandiri yang sudah selesai dilaksanakan.');
        }

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
            $sertifPath = $this->fileService->storeSertifikat((int) $pegawai->id, $sertifFile);
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

        return $this->responseMapper->mutation(
            $diklat,
            $jadwal,
            $kategori,
            $jenisDiklat,
            $isInternal ? (string) ($payload['jenis_biaya'] ?? '') : null
        );
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

            $statusPelaksanaan = $this->resolveStatusByTanggal($tanggalMulai, $tanggalSelesai);
            if ($statusPelaksanaan !== 'selesai') {
                throw new InvalidArgumentException('Pegawai hanya dapat mengubah riwayat diklat yang sudah selesai dilaksanakan.');
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
            $jadwal->status_kelayakan = 'layak';
            $jadwal->status_validasi = null;
        } else {
            $jadwal->status_validasi = null;
            $jadwal->status_kelayakan = null;
        }

        if (array_key_exists('no_sertif', $payload)) {
            $jadwal->no_sertif = (string) ($payload['no_sertif'] ?? '');
        }

        if ($sertifFile !== null) {
            $jadwal->sertif_file_path = $this->fileService->storeSertifikat((int) $pegawai->id, $sertifFile);
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

        return $this->responseMapper->mutation(
            $diklat,
            $jadwal,
            $kategori,
            $jenisDiklat,
            (string) ($diklat->jenisBiaya?->nama ?? '')
        );
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

        return $this->responseMapper->deleted($diklat, $jadwal);
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
}
