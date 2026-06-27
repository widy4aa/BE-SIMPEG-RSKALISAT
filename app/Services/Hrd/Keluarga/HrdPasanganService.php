<?php

namespace App\Services\Hrd\Keluarga;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class HrdPasanganService extends BaseHrdKeluargaService
{
    public function getAllPasangan(int $pegawaiId): array
    {
        $items = $this->repository->getPasanganByPegawaiId($pegawaiId)
            ->map(fn ($p) => array_merge($p->toArray(), [
                'link_buku_nikah' => $p->buku_nikah_file_path ? '/'.$p->buku_nikah_file_path : null,
            ]))->values()->toArray();

        return ['label' => 'Data Pasangan', 'total' => count($items), 'items' => $items];
    }

    public function createPasangan(int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);

        if ($file !== null) {
            $data['buku_nikah_file_path'] = $this->storeFile($file, 'dokumen/pasangan', 'buku_nikah');
        }

        $data['pegawai_pribadi_id'] = $pribadi->id;
        $data['status_tanggungan'] = $this->normalizeBoolean($data['status_tanggungan'] ?? false);

        $result = $this->repository->createPasangan($data);

        return ['id' => $result->id, 'nama_lengkap' => $result->nama_lengkap];
    }

    public function updatePasangan(int $id, int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $pasangan = $this->repository->findPasanganByIdAndPegawaiId($id, $pegawaiId);
        if ($pasangan === null) {
            throw new InvalidArgumentException('Data pasangan tidak ditemukan.');
        }

        if ($file !== null) {
            $this->deletePublicFile($pasangan->buku_nikah_file_path);
            $data['buku_nikah_file_path'] = $this->storeFile($file, 'dokumen/pasangan', 'buku_nikah');
        }

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = $this->normalizeBoolean($data['status_tanggungan']);
        }

        $updated = $this->repository->updatePasangan($pasangan, $data);

        return ['id' => $updated->id, 'nama_lengkap' => $updated->nama_lengkap];
    }

    public function deletePasangan(int $id, int $pegawaiId): array
    {
        $pasangan = $this->repository->findPasanganByIdAndPegawaiId($id, $pegawaiId);
        if ($pasangan === null) {
            throw new InvalidArgumentException('Data pasangan tidak ditemukan.');
        }

        $this->deletePublicFile($pasangan->buku_nikah_file_path);
        $this->repository->deletePasangan($pasangan);

        return ['id' => $id];
    }
}
