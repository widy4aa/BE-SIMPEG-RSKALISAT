<?php

namespace App\Services\RiwayatKarir\Managed;

use App\Repositories\RiwayatKarir\Managed\JabatanRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;

class JabatanService extends BaseRiwayatKarirService
{
    public function __construct(protected readonly JabatanRepository $repository) {}

    public function getJabatan(int $pegawaiId): array
    {
        $items = $this->repository->getJabatanByPegawaiId($pegawaiId)
            ->map(fn ($jp) => $this->formatJabatan($jp))->values()->toArray();

        return ['label' => 'Riwayat jabatan', 'total' => count($items), 'items' => $items];
    }

    public function createJabatan(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/jabatan', 'sk-jabatan', $pegawaiId, $file, null);

        $jabatanData = [
            'unit_kerja_id' => $payload['unit_kerja_id'] ?? null,
            'nama'          => (string) $payload['nama_jabatan'],
            'tmt_mulai'     => $payload['tmt_mulai'] ?? null,
            'tmt_selesai'   => $payload['tmt_selesai'] ?? null,
            'sk_file_path'  => $skFilePath,
        ];

        $pivotData = [
            'is_current' => (bool) $payload['is_current'],
            'started_at' => $payload['tmt_mulai'] ?? null,
            'ended_at'   => $payload['tmt_selesai'] ?? null,
            'note'       => $payload['note'] ?? null,
        ];

        $jp = $this->repository->createJabatanAndPivot($pegawai, $jabatanData, $pivotData);

        return $this->formatJabatan($jp->load('jabatan.unitKerja'));
    }

    public function updateJabatan(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $jp = $this->repository->findJabatanByIdAndPegawaiId($id, $pegawaiId);
        if ($jp === null) {
            throw new ModelNotFoundException('Data riwayat jabatan tidak ditemukan.');
        }

        $jabatanData = [];
        if (array_key_exists('unit_kerja_id', $payload)) $jabatanData['unit_kerja_id'] = $payload['unit_kerja_id'];
        if (array_key_exists('nama_jabatan', $payload))  $jabatanData['nama'] = (string) $payload['nama_jabatan'];
        if (array_key_exists('tmt_mulai', $payload))     $jabatanData['tmt_mulai'] = $payload['tmt_mulai'];
        if (array_key_exists('tmt_selesai', $payload))   $jabatanData['tmt_selesai'] = $payload['tmt_selesai'];

        $pivotData = [];
        if (array_key_exists('is_current', $payload)) $pivotData['is_current'] = (bool) $payload['is_current'];
        if (array_key_exists('tmt_mulai', $payload))  $pivotData['started_at'] = $payload['tmt_mulai'];
        if (array_key_exists('tmt_selesai', $payload)) $pivotData['ended_at'] = $payload['tmt_selesai'];
        if (array_key_exists('note', $payload))        $pivotData['note'] = $payload['note'];

        if ($file !== null) {
            $jabatanData['sk_file_path'] = $this->handleFileUpload('dokumen/jabatan', 'sk-jabatan', $pegawaiId, $file, $jp->jabatan?->sk_file_path);
        }

        $updated = $this->repository->updateJabatanAndPivot($jp, $jabatanData, $pivotData);

        return $this->formatJabatan($updated);
    }

    public function deleteJabatan(int $id, int $pegawaiId): array
    {
        $jp = $this->repository->findJabatanByIdAndPegawaiId($id, $pegawaiId);
        if ($jp === null) {
            throw new ModelNotFoundException('Data riwayat jabatan tidak ditemukan.');
        }

        $skPath = $jp->jabatan?->sk_file_path;
        if ($skPath && file_exists(public_path($skPath))) {
            @unlink(public_path($skPath));
        }

        $this->repository->deleteJabatanAndPivot($jp);

        return ['id' => $id];
    }

    private function formatJabatan($item): array
    {
        return [
            'id'             => $item->id,
            'unit_kerja_id'  => $item->jabatan?->unit_kerja_id,
            'unit_kerja_nama' => $item->jabatan?->unitKerja?->nama ?? '',
            'nama_jabatan'   => $item->jabatan?->nama ?? '',
            'is_current'     => (bool) $item->is_current,
            'tmt_mulai'      => $item->jabatan?->tmt_mulai?->format('Y-m-d') ?? $item->started_at?->format('Y-m-d'),
            'tmt_selesai'    => $item->jabatan?->tmt_selesai?->format('Y-m-d') ?? $item->ended_at?->format('Y-m-d'),
            'link_sk'        => $item->jabatan?->sk_file_path ? '/'.$item->jabatan->sk_file_path : null,
            'note'           => $item->note ?? '',
        ];
    }
}
