<?php

namespace App\Services\DataKeluarga;

use App\Repositories\DataKeluarga\AnakRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class AnakService
{
    public function __construct(private readonly AnakRepository $repository) {}

    public function getAllByUserId(int $userId): array
    {
        $anak = $this->repository->getAnakByUserId($userId);
        
        $items = $anak->map(function ($item) {
            $data = $item->toArray();
            if (!empty($item->akta_kelahiran_file_path)) {
                $data['link_akta_kelahiran'] = url('/' . $item->akta_kelahiran_file_path);
            } else {
                $data['link_akta_kelahiran'] = null;
            }
            return $data;
        })->toArray();

        return [
            'welcome' => 'Data anak berhasil diambil.',
            'summary' => [
                'label' => 'Data Anak',
                'total' => count($items),
                'items' => $items,
            ],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?UploadedFile $aktaFile = null): array
    {
        if ($userId <= 0) {
            throw new InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->repository->findPegawaiByUserIdWithPribadi($userId);
        if ($pegawai === null) {
            throw new InvalidArgumentException('Data pegawai untuk user login tidak ditemukan.');
        }

        $pribadi = $pegawai->pribadi;
        if ($pribadi === null) {
            $pribadi = $this->repository->createPegawaiPribadi((int) $pegawai->id);
        }

        $filePath = null;
        if ($aktaFile) {
            $fileName = uniqid('akta_kelahiran_').'.'.$aktaFile->getClientOriginalExtension();
            $aktaFile->move(public_path('dokumen/anak'), $fileName);
            $filePath = 'dokumen/anak/'.$fileName;
        }

        $payload['pegawai_pribadi_id'] = $pribadi->id;
        $payload['status_tanggungan'] = filter_var($payload['status_tanggungan'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($filePath) {
            $payload['akta_kelahiran_file_path'] = $filePath;
        }

        $newAnak = $this->repository->create($payload);

        return [
            'id' => $newAnak->id,
            'nama_lengkap' => $newAnak->nama_lengkap,
        ];
    }

    public function updateById(int $id, int $userId, array $payload, ?UploadedFile $aktaFile = null): array
    {
        $anak = $this->repository->findByIdAndUserId($id, $userId);

        if (!$anak) {
            throw new InvalidArgumentException('Data anak tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($aktaFile) {
            if ($anak->akta_kelahiran_file_path && file_exists(public_path($anak->akta_kelahiran_file_path))) {
                unlink(public_path($anak->akta_kelahiran_file_path));
            }

            $fileName = uniqid('akta_kelahiran_').'.'.$aktaFile->getClientOriginalExtension();
            $aktaFile->move(public_path('dokumen/anak'), $fileName);
            $payload['akta_kelahiran_file_path'] = 'dokumen/anak/'.$fileName;
        }

        $payload['status_tanggungan'] = filter_var($payload['status_tanggungan'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $this->repository->update($anak, $payload);

        return [
            'id' => $anak->id,
            'nama_lengkap' => $anak->nama_lengkap,
        ];
    }

    public function deleteById(int $id, int $userId): array
    {
        $anak = $this->repository->findByIdAndUserId($id, $userId);

        if (!$anak) {
            throw new InvalidArgumentException('Data anak tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($anak->akta_kelahiran_file_path && file_exists(public_path($anak->akta_kelahiran_file_path))) {
            unlink(public_path($anak->akta_kelahiran_file_path));
        }

        $this->repository->delete($anak);

        return [
            'id' => $id,
        ];
    }
}
