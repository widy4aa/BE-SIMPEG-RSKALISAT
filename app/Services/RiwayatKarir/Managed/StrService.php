<?php

namespace App\Services\RiwayatKarir\Managed;

use App\Repositories\RiwayatKarir\Managed\StrRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;

class StrService extends BaseRiwayatKarirService
{
    public function __construct(protected readonly StrRepository $repository) {}

    public function getStr(int $pegawaiId): array
    {
        $items = $this->repository->getStrByPegawaiId($pegawaiId)
            ->map(fn ($s) => $this->formatStr($s))->values()->toArray();

        return ['label' => 'Riwayat STR', 'total' => count($items), 'items' => $items];
    }

    public function createStr(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/str', 'sk-str', $pegawaiId, $file, null);

        $data = [
            'nomor_str'          => $payload['nomor_str'],
            'tanggal_terbit'     => $payload['tanggal_terbit'],
            'tanggal_kadaluarsa' => $payload['tanggal_kadaluarsa'] ?? null,
            'sk_file_path'       => $skFilePath,
        ];

        $str = $this->repository->createStr($pegawai, $data);

        return $this->formatStr($str);
    }

    public function updateStr(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $str = $this->repository->findStrByIdAndPegawaiId($id, $pegawaiId);
        if ($str === null) {
            throw new ModelNotFoundException('Data riwayat STR tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('nomor_str', $payload))          $data['nomor_str'] = $payload['nomor_str'];
        if (array_key_exists('tanggal_terbit', $payload))     $data['tanggal_terbit'] = $payload['tanggal_terbit'];
        if (array_key_exists('tanggal_kadaluarsa', $payload)) $data['tanggal_kadaluarsa'] = $payload['tanggal_kadaluarsa'];

        if ($file !== null) {
            $data['sk_file_path'] = $this->handleFileUpload('dokumen/str', 'sk-str', $pegawaiId, $file, $str->sk_file_path);
        }

        $updated = $this->repository->updateStr($str, $data);

        return $this->formatStr($updated);
    }

    public function deleteStr(int $id, int $pegawaiId): array
    {
        $str = $this->repository->findStrByIdAndPegawaiId($id, $pegawaiId);
        if ($str === null) {
            throw new ModelNotFoundException('Data riwayat STR tidak ditemukan.');
        }

        if ($str->sk_file_path && file_exists(public_path($str->sk_file_path))) {
            @unlink(public_path($str->sk_file_path));
        }

        $this->repository->deleteStr($str);

        return ['id' => $id];
    }

    private function formatStr($item): array
    {
        return [
            'id'                 => $item->id,
            'nomor_str'          => $item->nomor_str,
            'tanggal_terbit'     => $item->tanggal_terbit?->format('Y-m-d'),
            'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa?->format('Y-m-d'),
            'status'             => $this->resolveTanggalStatus($item->tanggal_kadaluarsa),
            'link_sk'            => $item->sk_file_path ? '/'.$item->sk_file_path : null,
        ];
    }
}
