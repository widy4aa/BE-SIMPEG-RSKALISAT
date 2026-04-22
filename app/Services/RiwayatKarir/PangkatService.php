<?php

namespace App\Services\RiwayatKarir;

use App\Repositories\RiwayatKarir\PangkatRepository;

class PangkatService
{
    public function __construct(
        private readonly PangkatRepository $pangkatRepository,
    ) {}

    public function getByUserId(int $userId): array
    {
        $pegawai = $this->pangkatRepository->findPegawaiByUserIdWithPangkat($userId);

        $pangkatList = $pegawai?->pangkatPegawai
            ?->sortByDesc('started_at')
            ->values()
            ->map(function ($item) {
                return $this->formatPangkatItem($item);
            });

        return [
            'label' => 'Riwayat pangkat',
            'total' => $pangkatList ? $pangkatList->count() : 0,
            'items' => $pangkatList ? $pangkatList->toArray() : [],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->pangkatRepository->findPegawaiByUserIdWithPangkat($userId);
        if ($pegawai === null) {
            throw new \InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        $skFilePath = null;
        if ($skFile !== null) {
            $folder = public_path('dokumen/pangkat');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sk-pangkat-%d-%d.%s',
                $pegawai->id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/pangkat/'.$filename;
        }

        $pangkatData = [
            'nama' => (string) $payload['nama_pangkat'],
            'pejabat_penetap' => $payload['pejabat_penetap'] ?? null,
            'tmt_sk' => $payload['tmt_sk'] ?? null,
            'sk_file_path' => $skFilePath,
        ];

        $pivotData = [
            'is_current' => (bool) $payload['is_current'],
            'started_at' => $payload['started_at'] ?? null,
            'ended_at' => $payload['ended_at'] ?? null,
            'note' => $payload['note'] ?? null,
        ];

        $pangkatPegawai = $this->pangkatRepository->createPangkatAndPivot($pegawai, $pangkatData, $pivotData);

        return $this->formatPangkatItem($pangkatPegawai);
    }

    public function updateByIdAndUserId(int $id, int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $pangkatPegawai = $this->pangkatRepository->findPangkatPegawaiByIdAndUserId($id, $userId);
        if ($pangkatPegawai === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat pangkat tidak ditemukan.');
        }

        $skFilePath = $pangkatPegawai->pangkat?->sk_file_path;
        if ($skFile !== null) {
            $folder = public_path('dokumen/pangkat');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            if ($skFilePath && file_exists(public_path($skFilePath))) {
                @unlink(public_path($skFilePath));
            }

            $filename = sprintf(
                'sk-pangkat-%d-%d.%s',
                $pangkatPegawai->pegawai_id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/pangkat/'.$filename;
        }

        $pangkatData = [];
        if (array_key_exists('nama_pangkat', $payload)) $pangkatData['nama'] = (string) $payload['nama_pangkat'];
        if (array_key_exists('pejabat_penetap', $payload)) $pangkatData['pejabat_penetap'] = $payload['pejabat_penetap'];
        if (array_key_exists('tmt_sk', $payload)) $pangkatData['tmt_sk'] = $payload['tmt_sk'];
        if ($skFile !== null) $pangkatData['sk_file_path'] = $skFilePath;

        $pivotData = [];
        if (array_key_exists('is_current', $payload)) $pivotData['is_current'] = (bool) $payload['is_current'];
        if (array_key_exists('started_at', $payload)) $pivotData['started_at'] = $payload['started_at'];
        if (array_key_exists('ended_at', $payload)) $pivotData['ended_at'] = $payload['ended_at'];
        if (array_key_exists('note', $payload)) $pivotData['note'] = $payload['note'];

        $updatedPangkatPegawai = $this->pangkatRepository->updatePangkatAndPivot($pangkatPegawai, $pangkatData, $pivotData);

        return $this->formatPangkatItem($updatedPangkatPegawai);
    }

    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $pangkatPegawai = $this->pangkatRepository->findPangkatPegawaiByIdAndUserId($id, $userId);
        if ($pangkatPegawai === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat pangkat tidak ditemukan.');
        }

        $skFilePath = $pangkatPegawai->pangkat?->sk_file_path;
        if ($skFilePath && file_exists(public_path($skFilePath))) {
            @unlink(public_path($skFilePath));
        }

        $this->pangkatRepository->deletePangkatAndPivot($pangkatPegawai);
    }

    private function formatPangkatItem($item): array
    {
        return [
            'id' => $item->id,
            'nama_pangkat' => $item->pangkat?->nama ?? '',
            'is_current' => (bool) $item->is_current,
            'pejabat_penetap' => $item->pangkat?->pejabat_penetap,
            'tmt_sk' => $item->pangkat?->tmt_sk?->format('Y-m-d'),
            'started_at' => $item->started_at?->format('Y-m-d'),
            'ended_at' => $item->ended_at?->format('Y-m-d'),
            'link_sk' => $item->pangkat?->sk_file_path ? url('/'.$item->pangkat->sk_file_path) : null,
            'note' => $item->note ?? '',
        ];
    }
}
