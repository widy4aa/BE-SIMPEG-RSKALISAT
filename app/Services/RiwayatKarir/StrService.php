<?php

namespace App\Services\RiwayatKarir;

use App\Repositories\RiwayatKarir\StrRepository;

class StrService
{
    public function __construct(
        private readonly StrRepository $strRepository,
    ) {}

    public function getByUserId(int $userId): array
    {
        $pegawai = $this->strRepository->findPegawaiByUserIdWithStr($userId);

        $strList = $pegawai?->str
            ?->sortByDesc('tanggal_terbit')
            ->values()
            ->map(function ($item) {
                return $this->formatStrItem($item);
            });

        return [
            'label' => 'Riwayat STR',
            'total' => $strList ? $strList->count() : 0,
            'items' => $strList ? $strList->toArray() : [],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->strRepository->findPegawaiByUserIdWithStr($userId);
        if ($pegawai === null) {
            throw new \InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        $skFilePath = null;
        if ($skFile !== null) {
            $folder = public_path('dokumen/str');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sk-str-%d-%d.%s',
                $pegawai->id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/str/'.$filename;
        }

        $data = [
            'nomor_str' => $payload['nomor_str'],
            'tanggal_terbit' => $payload['tanggal_terbit'],
            'tanggal_kadaluarsa' => $payload['tanggal_kadaluarsa'] ?? null,
            'is_current' => (bool) $payload['is_current'],
            'sk_file_path' => $skFilePath,
        ];

        $str = $this->strRepository->createStr($pegawai, $data);

        return $this->formatStrItem($str);
    }

    public function updateByIdAndUserId(int $id, int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $str = $this->strRepository->findStrByIdAndUserId($id, $userId);
        if ($str === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat STR tidak ditemukan.');
        }

        $skFilePath = $str->sk_file_path;
        if ($skFile !== null) {
            $folder = public_path('dokumen/str');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            if ($skFilePath && file_exists(public_path($skFilePath))) {
                @unlink(public_path($skFilePath));
            }

            $filename = sprintf(
                'sk-str-%d-%d.%s',
                $str->pegawai_id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/str/'.$filename;
        }

        $data = [];
        if (array_key_exists('nomor_str', $payload)) $data['nomor_str'] = $payload['nomor_str'];
        if (array_key_exists('tanggal_terbit', $payload)) $data['tanggal_terbit'] = $payload['tanggal_terbit'];
        if (array_key_exists('tanggal_kadaluarsa', $payload)) $data['tanggal_kadaluarsa'] = $payload['tanggal_kadaluarsa'];
        if (array_key_exists('is_current', $payload)) $data['is_current'] = (bool) $payload['is_current'];
        if ($skFile !== null) $data['sk_file_path'] = $skFilePath;

        $updatedStr = $this->strRepository->updateStr($str, $data);

        return $this->formatStrItem($updatedStr);
    }

    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $str = $this->strRepository->findStrByIdAndUserId($id, $userId);
        if ($str === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat STR tidak ditemukan.');
        }

        $skFilePath = $str->sk_file_path;
        if ($skFilePath && file_exists(public_path($skFilePath))) {
            @unlink(public_path($skFilePath));
        }

        $this->strRepository->deleteStr($str);
    }

    private function formatStrItem($item): array
    {
        return [
            'id' => $item->id,
            'nomor_str' => $item->nomor_str,
            'tanggal_terbit' => $item->tanggal_terbit?->format('Y-m-d'),
            'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa?->format('Y-m-d'),
            'is_current' => (bool) $item->is_current,
            'link_sk' => $item->sk_file_path ? url('/'.$item->sk_file_path) : null,
        ];
    }
}
