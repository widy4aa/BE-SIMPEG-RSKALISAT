<?php

namespace App\Services\Hrd\Keluarga;

use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

class HrdAnakService extends BaseHrdKeluargaService
{
    public function getAllAnak(int $pegawaiId): array
    {
        $items = $this->repository->getAnakByPegawaiId($pegawaiId)
            ->map(fn ($a) => array_merge($a->toArray(), [
                'link_akta_kelahiran' => $a->akta_kelahiran_file_path ? '/'.$a->akta_kelahiran_file_path : null,
            ]))->values()->toArray();

        return ['label' => 'Data Anak', 'total' => count($items), 'items' => $items];
    }

    public function createAnak(int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $pribadi = $this->repository->createPribadiIfNotExists($pegawaiId);

        if ($file !== null) {
            $data['akta_kelahiran_file_path'] = $this->storeFile($file, 'dokumen/anak', 'akta_kelahiran');
        }

        $data['pegawai_pribadi_id'] = $pribadi->id;
        $data['status_tanggungan'] = $this->normalizeBoolean($data['status_tanggungan'] ?? false);

        $result = $this->repository->createAnak($data);

        return ['id' => $result->id, 'nama_lengkap' => $result->nama_lengkap];
    }

    public function updateAnak(int $id, int $pegawaiId, array $data, ?UploadedFile $file = null): array
    {
        $anak = $this->repository->findAnakByIdAndPegawaiId($id, $pegawaiId);
        if ($anak === null) {
            throw new InvalidArgumentException('Data anak tidak ditemukan.');
        }

        if ($file !== null) {
            $this->deletePublicFile($anak->akta_kelahiran_file_path);
            $data['akta_kelahiran_file_path'] = $this->storeFile($file, 'dokumen/anak', 'akta_kelahiran');
        }

        if (array_key_exists('status_tanggungan', $data)) {
            $data['status_tanggungan'] = $this->normalizeBoolean($data['status_tanggungan']);
        }

        $updated = $this->repository->updateAnak($anak, $data);

        return ['id' => $updated->id, 'nama_lengkap' => $updated->nama_lengkap];
    }

    public function deleteAnak(int $id, int $pegawaiId): array
    {
        $anak = $this->repository->findAnakByIdAndPegawaiId($id, $pegawaiId);
        if ($anak === null) {
            throw new InvalidArgumentException('Data anak tidak ditemukan.');
        }

        $this->deletePublicFile($anak->akta_kelahiran_file_path);
        $this->repository->deleteAnak($anak);

        return ['id' => $id];
    }
}
