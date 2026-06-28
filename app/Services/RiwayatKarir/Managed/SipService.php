<?php

namespace App\Services\RiwayatKarir\Managed;

use App\Repositories\RiwayatKarir\Managed\SipRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;

class SipService extends BaseRiwayatKarirService
{
    public function __construct(protected readonly SipRepository $repository) {}

    public function getSip(int $pegawaiId): array
    {
        $items = $this->repository->getSipByPegawaiId($pegawaiId)
            ->map(fn ($s) => $this->formatSip($s))->values()->toArray();

        return ['label' => 'Riwayat SIP', 'total' => count($items), 'items' => $items];
    }

    public function createSip(int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $pegawai = $this->getPegawaiOrFail($pegawaiId);

        $skFilePath = $this->handleFileUpload('dokumen/sip', 'sk-sip', $pegawaiId, $file, null);

        $data = [
            'jenis_sip_id'       => $payload['jenis_sip_id'] ?? null,
            'nomor_sip'          => $payload['nomor_sip'],
            'tanggal_terbit'     => $payload['tanggal_terbit'],
            'tanggal_kadaluarsa' => $payload['tanggal_kadaluarsa'] ?? null,
            'sk_file_path'       => $skFilePath,
        ];

        $sip = $this->repository->createSip($pegawai, $data);

        return $this->formatSip($sip);
    }

    public function updateSip(int $id, int $pegawaiId, array $payload, ?UploadedFile $file = null): array
    {
        $sip = $this->repository->findSipByIdAndPegawaiId($id, $pegawaiId);
        if ($sip === null) {
            throw new ModelNotFoundException('Data riwayat SIP tidak ditemukan.');
        }

        $data = [];
        if (array_key_exists('jenis_sip_id', $payload))       $data['jenis_sip_id'] = $payload['jenis_sip_id'];
        if (array_key_exists('nomor_sip', $payload))           $data['nomor_sip'] = $payload['nomor_sip'];
        if (array_key_exists('tanggal_terbit', $payload))      $data['tanggal_terbit'] = $payload['tanggal_terbit'];
        if (array_key_exists('tanggal_kadaluarsa', $payload))  $data['tanggal_kadaluarsa'] = $payload['tanggal_kadaluarsa'];

        if ($file !== null) {
            $data['sk_file_path'] = $this->handleFileUpload('dokumen/sip', 'sk-sip', $pegawaiId, $file, $sip->sk_file_path);
        }

        $updated = $this->repository->updateSip($sip, $data);

        return $this->formatSip($updated);
    }

    public function deleteSip(int $id, int $pegawaiId): array
    {
        $sip = $this->repository->findSipByIdAndPegawaiId($id, $pegawaiId);
        if ($sip === null) {
            throw new ModelNotFoundException('Data riwayat SIP tidak ditemukan.');
        }

        if ($sip->sk_file_path && file_exists(public_path($sip->sk_file_path))) {
            @unlink(public_path($sip->sk_file_path));
        }

        $this->repository->deleteSip($sip);

        return ['id' => $id];
    }

    private function formatSip($item): array
    {
        return [
            'id'                 => $item->id,
            'jenis_sip_id'       => $item->jenis_sip_id,
            'jenis_sip_nama'     => $item->jenisSip?->nama ?? '',
            'nomor_sip'          => $item->nomor_sip,
            'tanggal_terbit'     => $item->tanggal_terbit?->format('Y-m-d'),
            'tanggal_kadaluarsa' => $item->tanggal_kadaluarsa?->format('Y-m-d'),
            'status'             => $this->resolveTanggalStatus($item->tanggal_kadaluarsa),
            'link_sk'            => $item->sk_file_path ? '/'.$item->sk_file_path : null,
        ];
    }
}
