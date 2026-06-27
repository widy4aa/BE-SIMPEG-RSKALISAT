<?php

namespace App\Services\Hrd\RiwayatKarir;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;

class HrdPenugasanKlinisService extends BaseHrdRiwayatKarirService
{
    public function getPenugasanKlinis(int $pegawaiId): array
    {
        $items = $this->repository->getPenugasanKlinisByPegawaiId($pegawaiId)
            ->map(fn ($pk) => $this->formatPenugasanKlinis($pk))->values()->toArray();

        return ['label' => 'Riwayat Penugasan Klinis', 'total' => count($items), 'items' => $items];
    }

    public function createPenugasanKlinis(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $filePath = $this->handleFileUpload('dokumen/penugasan-klinis', 'sk-penugasan-klinis', $pegawaiId, $file, null);

        $data = [
            'nomor_surat' => $payload['nomor_surat'],
            'tgl_mulai' => $payload['tgl_mulai'],
            'tgl_kadaluarsa' => $payload['tgl_kadaluarsa'] ?? null,
            'is_current' => (bool) $payload['is_current'],
            'dokumen_file_path' => $filePath,
        ];

        $pk = $this->repository->createPenugasanKlinis($pegawai, $data);

        return $this->formatPenugasanKlinis($pk);
    }

    public function updatePenugasanKlinis(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pk = $this->repository->findPenugasanKlinisByIdAndPegawaiId($id, $pegawaiId);
        if ($pk === null) {
            throw new ModelNotFoundException('Data riwayat penugasan klinis tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('nomor_surat', $payload)) {
            $data['nomor_surat'] = $payload['nomor_surat'];
        }
        if (array_key_exists('tgl_mulai', $payload)) {
            $data['tgl_mulai'] = $payload['tgl_mulai'];
        }
        if (array_key_exists('tgl_kadaluarsa', $payload)) {
            $data['tgl_kadaluarsa'] = $payload['tgl_kadaluarsa'];
        }
        if (array_key_exists('is_current', $payload)) {
            $data['is_current'] = (bool) $payload['is_current'];
        }

        if ($file !== null) {
            $data['dokumen_file_path'] = $this->handleFileUpload('dokumen/penugasan-klinis', 'sk-penugasan-klinis', $pegawaiId, $file, $pk->dokumen_file_path);
        }

        $updated = $this->repository->updatePenugasanKlinis($pk, $data);

        return $this->formatPenugasanKlinis($updated);
    }

    public function deletePenugasanKlinis(int $id, int $pegawaiId): array
    {
        $pk = $this->repository->findPenugasanKlinisByIdAndPegawaiId($id, $pegawaiId);
        if ($pk === null) {
            throw new ModelNotFoundException('Data riwayat penugasan klinis tidak ditemukan.');
        }

        if ($pk->dokumen_file_path && file_exists(public_path($pk->dokumen_file_path))) {
            @unlink(public_path($pk->dokumen_file_path));
        }

        $this->repository->deletePenugasanKlinis($pk);

        return ['id' => $id];
    }

    private function formatPenugasanKlinis($item): array
    {
        return [
            'id' => $item->id,
            'nomor_surat' => $item->nomor_surat,
            'tgl_mulai' => $item->tgl_mulai?->format('Y-m-d'),
            'tgl_kadaluarsa' => $item->tgl_kadaluarsa?->format('Y-m-d'),
            'is_current' => (bool) $item->is_current,
            'link_dokumen' => $item->dokumen_file_path ? '/'.$item->dokumen_file_path : null,
        ];
    }
}
