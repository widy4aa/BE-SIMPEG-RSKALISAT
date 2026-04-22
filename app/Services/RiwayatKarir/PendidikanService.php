<?php

namespace App\Services\RiwayatKarir;

use App\Repositories\RiwayatKarir\PendidikanRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class PendidikanService
{
    public function __construct(
        private readonly PendidikanRepository $pendidikanRepository,
    ) {}

    public function getByUserId(int $userId): array
    {
        $pegawai = $this->pendidikanRepository->findPegawaiByUserIdWithPendidikan($userId);

        $pendidikan = $pegawai?->pribadi?->pendidikan
            ?->sortByDesc(fn ($item) => sprintf('%04d-%010d', (int) ($item->tahun_lulus ?? 0), (int) $item->id))
            ->values()
            ->map(function ($item) {
                return [
                    'id' => (int) $item->id,
                    'jenjang' => (string) ($item->jenjang ?? ''),
                    'institusi' => (string) ($item->institusi ?? ''),
                    'jurusan' => (string) ($item->jurusan ?? ''),
                    'tahun_lulus' => $item->tahun_lulus !== null ? (int) $item->tahun_lulus : null,
                    'nomor_ijazah' => (string) ($item->nomor_ijazah ?? ''),
                    'link_ijazah' => $item->ijazah_file_path ? url('/'.$item->ijazah_file_path) : null,
                ];
            })
            ->all() ?? [];

        return [
            'welcome' => 'Data riwayat pendidikan berhasil diambil.',
            'summary' => [
                'label' => 'Riwayat pendidikan',
                'total' => count($pendidikan),
                'items' => $pendidikan,
            ],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?UploadedFile $ijazahFile = null): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pendidikanRepository->findPegawaiByUserIdWithPribadi($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $pribadi = $pegawai->pribadi;
        if ($pribadi === null) {
            $pribadi = $this->pendidikanRepository->createPegawaiPribadi((int) $pegawai->id);
        }

        $ijazahFilePath = null;
        if ($ijazahFile !== null) {
            $folder = public_path('dokumen/ijazah');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'ijazah-%d-%d.%s',
                (int) $pegawai->id,
                time(),
                $ijazahFile->getClientOriginalExtension()
            );

            $ijazahFile->move($folder, $filename);
            $ijazahFilePath = 'dokumen/ijazah/'.$filename;
        }

        $pendidikan = $this->pendidikanRepository->createPendidikan([
            'pegawai_pribadi_id' => (int) $pribadi->id,
            'jenjang' => (string) ($payload['jenjang'] ?? ''),
            'institusi' => (string) ($payload['institusi'] ?? ''),
            'jurusan' => (string) ($payload['jurusan'] ?? ''),
            'tahun_lulus' => isset($payload['tahun_lulus']) ? (int) $payload['tahun_lulus'] : null,
            'nomor_ijazah' => (string) ($payload['nomor_ijazah'] ?? ''),
            'ijazah_file_path' => $ijazahFilePath,
        ]);

        return [
            'id' => (int) $pendidikan->id,
            'jenjang' => (string) ($pendidikan->jenjang ?? ''),
            'institusi' => (string) ($pendidikan->institusi ?? ''),
            'jurusan' => (string) ($pendidikan->jurusan ?? ''),
            'tahun_lulus' => $pendidikan->tahun_lulus !== null ? (int) $pendidikan->tahun_lulus : null,
            'nomor_ijazah' => (string) ($pendidikan->nomor_ijazah ?? ''),
            'link_ijazah' => $pendidikan->ijazah_file_path ? url('/'.$pendidikan->ijazah_file_path) : null,
        ];
    }

    public function updateByIdAndUserId(int $id, int $userId, array $payload, ?UploadedFile $ijazahFile = null): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pendidikan = $this->pendidikanRepository->findByIdAndUserId($id, $userId);
        if ($pendidikan === null) {
            throw new ModelNotFoundException('Data riwayat pendidikan tidak ditemukan.');
        }

        $ijazahFilePath = $pendidikan->ijazah_file_path;
        if ($ijazahFile !== null) {
            $folder = public_path('dokumen/ijazah');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            if ($ijazahFilePath && file_exists(public_path($ijazahFilePath))) {
                @unlink(public_path($ijazahFilePath));
            }

            $filename = sprintf(
                'ijazah-%d-%d.%s',
                (int) $pendidikan->pegawai_pribadi_id,
                time(),
                $ijazahFile->getClientOriginalExtension()
            );

            $ijazahFile->move($folder, $filename);
            $ijazahFilePath = 'dokumen/ijazah/'.$filename;
        }

        $updateData = [];
        if (array_key_exists('jenjang', $payload)) {
            $updateData['jenjang'] = (string) $payload['jenjang'];
        }
        if (array_key_exists('institusi', $payload)) {
            $updateData['institusi'] = (string) $payload['institusi'];
        }
        if (array_key_exists('jurusan', $payload)) {
            $updateData['jurusan'] = (string) $payload['jurusan'];
        }
        if (array_key_exists('tahun_lulus', $payload)) {
            $updateData['tahun_lulus'] = isset($payload['tahun_lulus']) ? (int) $payload['tahun_lulus'] : null;
        }
        if (array_key_exists('nomor_ijazah', $payload)) {
            $updateData['nomor_ijazah'] = (string) $payload['nomor_ijazah'];
        }
        if ($ijazahFile !== null) {
            $updateData['ijazah_file_path'] = $ijazahFilePath;
        }

        $pendidikan = $this->pendidikanRepository->updatePendidikan($pendidikan, $updateData);

        return [
            'id' => (int) $pendidikan->id,
            'jenjang' => (string) ($pendidikan->jenjang ?? ''),
            'institusi' => (string) ($pendidikan->institusi ?? ''),
            'jurusan' => (string) ($pendidikan->jurusan ?? ''),
            'tahun_lulus' => $pendidikan->tahun_lulus !== null ? (int) $pendidikan->tahun_lulus : null,
            'nomor_ijazah' => (string) ($pendidikan->nomor_ijazah ?? ''),
            'link_ijazah' => $pendidikan->ijazah_file_path ? url('/'.$pendidikan->ijazah_file_path) : null,
        ];
    }

    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pendidikan = $this->pendidikanRepository->findByIdAndUserId($id, $userId);
        if ($pendidikan === null) {
            throw new ModelNotFoundException('Data riwayat pendidikan tidak ditemukan.');
        }

        $ijazahFilePath = $pendidikan->ijazah_file_path;
        if ($ijazahFilePath && file_exists(public_path($ijazahFilePath))) {
            @unlink(public_path($ijazahFilePath));
        }

        $this->pendidikanRepository->deletePendidikan($pendidikan);
    }
}
