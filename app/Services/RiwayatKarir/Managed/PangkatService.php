<?php

namespace App\Services\RiwayatKarir\Managed;

use App\Repositories\RiwayatKarir\Managed\PangkatRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;

class PangkatService extends BaseRiwayatKarirService
{
    public function __construct(protected readonly PangkatRepository $repository) {}

    public function getPangkat(int $pegawaiId): array
    {
        $items = $this->repository->getPangkatByPegawaiId($pegawaiId)
            ->map(fn ($pp) => $this->formatPangkat($pp))->values()->toArray();

        return ['label' => 'Riwayat pangkat', 'total' => count($items), 'items' => $items];
    }

    public function createPangkat(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/pangkat', 'sk-pangkat', $pegawaiId, $file, null);

        $pangkatData = [
            'nama'            => (string) $payload['nama_pangkat'],
            'pejabat_penetap' => $payload['pejabat_penetap'] ?? null,
            'tmt_sk'          => $payload['tmt_sk'] ?? null,
            'sk_file_path'    => $skFilePath,
        ];

        $pivotData = [
            'is_current' => (bool) $payload['is_current'],
            'started_at' => $payload['started_at'] ?? null,
            'ended_at'   => $payload['ended_at'] ?? null,
            'note'       => $payload['note'] ?? null,
        ];

        $pp = $this->repository->createPangkatAndPivot($pegawai, $pangkatData, $pivotData);

        return $this->formatPangkat($pp);
    }

    public function updatePangkat(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pp = $this->repository->findPangkatByIdAndPegawaiId($id, $pegawaiId);
        if ($pp === null) {
            throw new ModelNotFoundException('Data riwayat pangkat tidak ditemukan.');
        }

        $pangkatData = [];
        if (array_key_exists('nama_pangkat', $payload))    $pangkatData['nama'] = (string) $payload['nama_pangkat'];
        if (array_key_exists('pejabat_penetap', $payload)) $pangkatData['pejabat_penetap'] = $payload['pejabat_penetap'];
        if (array_key_exists('tmt_sk', $payload))          $pangkatData['tmt_sk'] = $payload['tmt_sk'];

        $pivotData = [];
        if (array_key_exists('is_current', $payload)) $pivotData['is_current'] = (bool) $payload['is_current'];
        if (array_key_exists('started_at', $payload))  $pivotData['started_at'] = $payload['started_at'];
        if (array_key_exists('ended_at', $payload))    $pivotData['ended_at'] = $payload['ended_at'];
        if (array_key_exists('note', $payload))        $pivotData['note'] = $payload['note'];

        if ($file !== null) {
            $pangkatData['sk_file_path'] = $this->handleFileUpload('dokumen/pangkat', 'sk-pangkat', $pegawaiId, $file, $pp->pangkat?->sk_file_path);
        }

        $updated = $this->repository->updatePangkatAndPivot($pp, $pangkatData, $pivotData);

        return $this->formatPangkat($updated);
    }

    public function deletePangkat(int $id, int $pegawaiId): array
    {
        $pp = $this->repository->findPangkatByIdAndPegawaiId($id, $pegawaiId);
        if ($pp === null) {
            throw new ModelNotFoundException('Data riwayat pangkat tidak ditemukan.');
        }

        $skPath = $pp->pangkat?->sk_file_path;
        if ($skPath && file_exists(public_path($skPath))) {
            @unlink(public_path($skPath));
        }

        $this->repository->deletePangkatAndPivot($pp);

        return ['id' => $id];
    }

    private function formatPangkat($item): array
    {
        return [
            'id'              => $item->id,
            'nama_pangkat'    => $item->pangkat?->nama ?? '',
            'is_current'      => (bool) $item->is_current,
            'pejabat_penetap' => $item->pangkat?->pejabat_penetap,
            'tmt_sk'          => $item->pangkat?->tmt_sk?->format('Y-m-d'),
            'started_at'      => $item->started_at?->format('Y-m-d'),
            'ended_at'        => $item->ended_at?->format('Y-m-d'),
            'link_sk'         => $item->pangkat?->sk_file_path ? '/'.$item->pangkat->sk_file_path : null,
            'note'            => $item->note ?? '',
        ];
    }
}
