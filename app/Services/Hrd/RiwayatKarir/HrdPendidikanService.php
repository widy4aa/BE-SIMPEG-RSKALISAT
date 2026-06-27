<?php

namespace App\Services\Hrd\RiwayatKarir;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;

class HrdPendidikanService extends BaseHrdRiwayatKarirService
{
    public function getPendidikan(int $pegawaiId): array
    {
        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
        $items = $pribadi
            ? $this->repository->getPendidikanByPribadiId($pribadi->id)
                ->map(fn ($p) => $this->formatPendidikan($p))->values()->toArray()
            : [];

        return ['label' => 'Riwayat pendidikan', 'total' => count($items), 'items' => $items];
    }

    public function createPendidikan(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $this->getPegawaiOrFail($pegawaiId);

        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId)
            ?? $this->repository->createPribadi($pegawaiId);

        $ijazahPath = $this->handleFileUpload('dokumen/ijazah', 'ijazah', $pegawaiId, $file, null);

        $data = [
            'jenjang' => $payload['jenjang'],
            'institusi' => $payload['institusi'],
            'jurusan' => $payload['jurusan'],
            'tahun_lulus' => (int) $payload['tahun_lulus'],
            'nomor_ijazah' => $payload['nomor_ijazah'] ?? null,
            'ijazah_file_path' => $ijazahPath,
        ];

        $pendidikan = $this->repository->createPendidikan($pribadi->id, $data);

        return $this->formatPendidikan($pendidikan);
    }

    public function updatePendidikan(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            throw new ModelNotFoundException('Data pribadi pegawai tidak ditemukan.');
        }

        $pendidikan = $this->repository->findPendidikanByIdAndPribadiId($id, $pribadi->id);
        if ($pendidikan === null) {
            throw new ModelNotFoundException('Data riwayat pendidikan tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('jenjang', $payload)) {
            $data['jenjang'] = $payload['jenjang'];
        }
        if (array_key_exists('institusi', $payload)) {
            $data['institusi'] = $payload['institusi'];
        }
        if (array_key_exists('jurusan', $payload)) {
            $data['jurusan'] = $payload['jurusan'];
        }
        if (array_key_exists('tahun_lulus', $payload)) {
            $data['tahun_lulus'] = (int) $payload['tahun_lulus'];
        }
        if (array_key_exists('nomor_ijazah', $payload)) {
            $data['nomor_ijazah'] = $payload['nomor_ijazah'];
        }

        if ($file !== null) {
            $data['ijazah_file_path'] = $this->handleFileUpload('dokumen/ijazah', 'ijazah', $pegawaiId, $file, $pendidikan->ijazah_file_path);
        }

        $updated = $this->repository->updatePendidikan($pendidikan, $data);

        return $this->formatPendidikan($updated);
    }

    public function deletePendidikan(int $id, int $pegawaiId): array
    {
        $pribadi = $this->repository->findPribadiByPegawaiId($pegawaiId);
        if ($pribadi === null) {
            throw new ModelNotFoundException('Data pribadi pegawai tidak ditemukan.');
        }

        $pendidikan = $this->repository->findPendidikanByIdAndPribadiId($id, $pribadi->id);
        if ($pendidikan === null) {
            throw new ModelNotFoundException('Data riwayat pendidikan tidak ditemukan.');
        }

        if ($pendidikan->ijazah_file_path && file_exists(public_path($pendidikan->ijazah_file_path))) {
            @unlink(public_path($pendidikan->ijazah_file_path));
        }

        $this->repository->deletePendidikan($pendidikan);

        return ['id' => $id];
    }

    private function formatPendidikan($item): array
    {
        return [
            'id' => $item->id,
            'jenjang' => $item->jenjang,
            'institusi' => $item->institusi,
            'jurusan' => $item->jurusan,
            'tahun_lulus' => $item->tahun_lulus,
            'nomor_ijazah' => $item->nomor_ijazah,
            'link_ijazah' => $item->ijazah_file_path ? '/'.$item->ijazah_file_path : null,
        ];
    }
}
