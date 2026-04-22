<?php

namespace App\Services\RiwayatKarir;

use App\Repositories\RiwayatKarir\SipRepository;

class SipService
{
    public function __construct(
        private readonly SipRepository $sipRepository,
    ) {}

    public function getByUserId(int $userId): array
    {
        $pegawai = $this->sipRepository->findPegawaiByUserIdWithSip($userId);

        $sipList = $pegawai?->sip
            ?->sortByDesc('tanggal_terbit')
            ->values()
            ->map(function ($item) {
                return $this->formatSipItem($item);
            });

        return [
            'label' => 'Riwayat SIP',
            'total' => $sipList ? $sipList->count() : 0,
            'items' => $sipList ? $sipList->toArray() : [],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->sipRepository->findPegawaiByUserIdWithSip($userId);
        if ($pegawai === null) {
            throw new \InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        $skFilePath = null;
        if ($skFile !== null) {
            $folder = public_path('dokumen/sip');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sk-sip-%d-%d.%s',
                $pegawai->id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/sip/'.$filename;
        }

        $data = [
            'jenis_sip_id' => $payload['jenis_sip_id'] ?? null,
            'nomor_sip' => $payload['nomor_sip'],
            'tanggal_terbit' => $payload['tanggal_terbit'],
            'tanggal_kadaluarsa' => $payload['tanggal_kadaluarsa'] ?? null,
            'is_current' => (bool) $payload['is_current'],
            'sk_file_path' => $skFilePath,
        ];

        $sip = $this->sipRepository->createSip($pegawai, $data);

        return $this->formatSipItem($sip);
    }

    public function updateByIdAndUserId(int $id, int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $sip = $this->sipRepository->findSipByIdAndUserId($id, $userId);
        if ($sip === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat SIP tidak ditemukan.');
        }

        $skFilePath = $sip->sk_file_path;
        if ($skFile !== null) {
            $folder = public_path('dokumen/sip');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            if ($skFilePath && file_exists(public_path($skFilePath))) {
                @unlink(public_path($skFilePath));
            }

            $filename = sprintf(
                'sk-sip-%d-%d.%s',
                $sip->pegawai_id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/sip/'.$filename;
        }

        $data = [];
        if (array_key_exists('jenis_sip_id', $payload)) $data['jenis_sip_id'] = $payload['jenis_sip_id'];
        if (array_key_exists('nomor_sip', $payload)) $data['nomor_sip'] = $payload['nomor_sip'];
        if (array_key_exists('tanggal_terbit', $payload)) $data['tanggal_terbit'] = $payload['tanggal_terbit'];
        if (array_key_exists('tanggal_kadaluarsa', $payload)) $data['tanggal_kadaluarsa'] = $payload['tanggal_kadaluarsa'];
        if (array_key_exists('is_current', $payload)) $data['is_current'] = (bool) $payload['is_current'];
        if ($skFile !== null) $data['sk_file_path'] = $skFilePath;

        $updatedSip = $this->sipRepository->updateSip($sip, $data);

        return $this->formatSipItem($updatedSip);
    }

    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $sip = $this->sipRepository->findSipByIdAndUserId($id, $userId);
        if ($sip === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat SIP tidak ditemukan.');
        }

        $skFilePath = $sip->sk_file_path;
        if ($skFilePath && file_exists(public_path($skFilePath))) {
            @unlink(public_path($skFilePath));
        }

        $this->sipRepository->deleteSip($sip);
    }

    private function formatSipItem($item): array
    {
        return [
            'id' => $item->id,
            'jenis_sip_id' => $item->jenis_sip_id,
            'jenis_sip_nama' => $item->jenisSip?->nama ?? '',
            'nomor_sip' => $item->nomor_sip,
            'tanggal_terbit' => $item->tanggal_terbit?->format('Y-m-d'),
            'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa?->format('Y-m-d'),
            'is_current' => (bool) $item->is_current,
            'link_sk' => $item->sk_file_path ? url('/'.$item->sk_file_path) : null,
        ];
    }
}
