<?php

namespace App\Services\RiwayatKarir;

use App\Repositories\RiwayatKarir\JabatanRepository;

class JabatanService
{
    public function __construct(
        private readonly JabatanRepository $jabatanRepository,
    ) {}

    public function getByUserId(int $userId): array
    {
        $pegawai = $this->jabatanRepository->findPegawaiByUserIdWithJabatan($userId);

        $jabatanList = $pegawai?->jabatanPegawai
            ?->sortByDesc('started_at')
            ->values()
            ->map(function ($item) {
                return $this->formatJabatanItem($item);
            });

        return [
            'label' => 'Riwayat jabatan',
            'total' => $jabatanList ? $jabatanList->count() : 0,
            'items' => $jabatanList ? $jabatanList->toArray() : [],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->jabatanRepository->findPegawaiByUserIdWithJabatan($userId);
        if ($pegawai === null) {
            throw new \InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        $skFilePath = null;
        if ($skFile !== null) {
            $folder = public_path('dokumen/jabatan');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sk-jabatan-%d-%d.%s',
                $pegawai->id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/jabatan/'.$filename;
        }

        $jabatanData = [
            'unit_kerja_id' => isset($payload['unit_kerja_id']) ? $payload['unit_kerja_id'] : null,
            'nama' => (string) $payload['nama_jabatan'],
            'tmt_mulai' => isset($payload['tmt_mulai']) ? $payload['tmt_mulai'] : null,
            'tmt_selesai' => isset($payload['tmt_selesai']) ? $payload['tmt_selesai'] : null,
            'sk_file_path' => $skFilePath,
        ];

        $pivotData = [
            'is_current' => (bool) $payload['is_current'],
            'started_at' => isset($payload['tmt_mulai']) ? $payload['tmt_mulai'] : null,
            'ended_at' => isset($payload['tmt_selesai']) ? $payload['tmt_selesai'] : null,
            'note' => $payload['note'] ?? null,
        ];

        $jabatanPegawai = $this->jabatanRepository->createJabatanAndPivot($pegawai, $jabatanData, $pivotData);

        return $this->formatJabatanItem($jabatanPegawai);
    }

    public function updateByIdAndUserId(int $id, int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $jabatanPegawai = $this->jabatanRepository->findJabatanPegawaiByIdAndUserId($id, $userId);
        if ($jabatanPegawai === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat jabatan tidak ditemukan.');
        }

        $skFilePath = $jabatanPegawai->jabatan?->sk_file_path;
        if ($skFile !== null) {
            $folder = public_path('dokumen/jabatan');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            if ($skFilePath && file_exists(public_path($skFilePath))) {
                @unlink(public_path($skFilePath));
            }

            $filename = sprintf(
                'sk-jabatan-%d-%d.%s',
                $jabatanPegawai->pegawai_id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/jabatan/'.$filename;
        }

        $jabatanData = [];
        if (array_key_exists('unit_kerja_id', $payload)) $jabatanData['unit_kerja_id'] = $payload['unit_kerja_id'];
        if (array_key_exists('nama_jabatan', $payload)) $jabatanData['nama'] = (string) $payload['nama_jabatan'];
        if (array_key_exists('tmt_mulai', $payload)) $jabatanData['tmt_mulai'] = $payload['tmt_mulai'];
        if (array_key_exists('tmt_selesai', $payload)) $jabatanData['tmt_selesai'] = $payload['tmt_selesai'];
        if ($skFile !== null) $jabatanData['sk_file_path'] = $skFilePath;

        $pivotData = [];
        if (array_key_exists('is_current', $payload)) $pivotData['is_current'] = (bool) $payload['is_current'];
        if (array_key_exists('tmt_mulai', $payload)) $pivotData['started_at'] = $payload['tmt_mulai'];
        if (array_key_exists('tmt_selesai', $payload)) $pivotData['ended_at'] = $payload['tmt_selesai'];
        if (array_key_exists('note', $payload)) $pivotData['note'] = $payload['note'];

        $updatedJabatanPegawai = $this->jabatanRepository->updateJabatanAndPivot($jabatanPegawai, $jabatanData, $pivotData);

        return $this->formatJabatanItem($updatedJabatanPegawai);
    }

    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $jabatanPegawai = $this->jabatanRepository->findJabatanPegawaiByIdAndUserId($id, $userId);
        if ($jabatanPegawai === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat jabatan tidak ditemukan.');
        }

        $skFilePath = $jabatanPegawai->jabatan?->sk_file_path;
        if ($skFilePath && file_exists(public_path($skFilePath))) {
            @unlink(public_path($skFilePath));
        }

        $this->jabatanRepository->deleteJabatanAndPivot($jabatanPegawai);
    }

    private function formatJabatanItem($item): array
    {
        return [
            'id' => $item->id,
            'unit_kerja_id' => $item->jabatan?->unit_kerja_id,
            'unit_kerja_nama' => $item->jabatan?->unitKerja?->nama ?? '',
            'nama_jabatan' => $item->jabatan?->nama ?? '',
            'is_current' => (bool) $item->is_current,
            'tmt_mulai' => $item->jabatan?->tmt_mulai?->format('Y-m-d') ?? $item->started_at?->format('Y-m-d'),
            'tmt_selesai' => $item->jabatan?->tmt_selesai?->format('Y-m-d') ?? $item->ended_at?->format('Y-m-d'),
            'link_sk' => $item->jabatan?->sk_file_path ? url('/'.$item->jabatan->sk_file_path) : null,
            'note' => $item->note ?? '',
        ];
    }
}
