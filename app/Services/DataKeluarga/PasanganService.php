<?php

namespace App\Services\DataKeluarga;

use App\Repositories\DataKeluarga\PasanganRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class PasanganService
{
    public function __construct(private readonly PasanganRepository $repository) {}

    public function getAllByUserId(int $userId): array
    {
        $pasangan = $this->repository->getPasanganByUserId($userId);
        
        $items = $pasangan->map(function ($item) {
            $data = $item->toArray();
            if (!empty($item->buku_nikah_file_path)) {
                $data['link_buku_nikah'] = url('/' . $item->buku_nikah_file_path);
            } else {
                $data['link_buku_nikah'] = null;
            }
            return $data;
        })->toArray();

        return [
            'welcome' => 'Data pasangan berhasil diambil.',
            'summary' => [
                'label' => 'Data Pasangan',
                'total' => count($items),
                'items' => $items,
            ],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?UploadedFile $bukuNikahFile = null): array
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
        if ($bukuNikahFile) {
            $fileName = uniqid('buku_nikah_').'.'.$bukuNikahFile->getClientOriginalExtension();
            $bukuNikahFile->move(public_path('dokumen/pasangan'), $fileName);
            $filePath = 'dokumen/pasangan/'.$fileName;
        }

        $payload['pegawai_pribadi_id'] = $pribadi->id;
        $payload['status_tanggungan'] = filter_var($payload['status_tanggungan'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if ($filePath) {
            $payload['buku_nikah_file_path'] = $filePath;
        }

        $newPasangan = $this->repository->create($payload);

        return [
            'id' => $newPasangan->id,
            'nama_lengkap' => $newPasangan->nama_lengkap,
        ];
    }

    public function updateById(int $id, int $userId, array $payload, ?UploadedFile $bukuNikahFile = null): array
    {
        $pasangan = $this->repository->findByIdAndUserId($id, $userId);

        if (!$pasangan) {
            throw new InvalidArgumentException('Data pasangan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($bukuNikahFile) {
            if ($pasangan->buku_nikah_file_path && file_exists(public_path($pasangan->buku_nikah_file_path))) {
                unlink(public_path($pasangan->buku_nikah_file_path));
            }

            $fileName = uniqid('buku_nikah_').'.'.$bukuNikahFile->getClientOriginalExtension();
            $bukuNikahFile->move(public_path('dokumen/pasangan'), $fileName);
            $payload['buku_nikah_file_path'] = 'dokumen/pasangan/'.$fileName;
        }

        $payload['status_tanggungan'] = filter_var($payload['status_tanggungan'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $this->repository->update($pasangan, $payload);

        return [
            'id' => $pasangan->id,
            'nama_lengkap' => $pasangan->nama_lengkap,
        ];
    }

    public function deleteById(int $id, int $userId): array
    {
        $pasangan = $this->repository->findByIdAndUserId($id, $userId);

        if (!$pasangan) {
            throw new InvalidArgumentException('Data pasangan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        if ($pasangan->buku_nikah_file_path && file_exists(public_path($pasangan->buku_nikah_file_path))) {
            unlink(public_path($pasangan->buku_nikah_file_path));
        }

        $this->repository->delete($pasangan);

        return [
            'id' => $id,
        ];
    }
}
