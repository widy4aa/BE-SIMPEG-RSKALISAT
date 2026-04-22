<?php

namespace App\Services\RiwayatKarir;

use App\Repositories\RiwayatKarir\PenugasanKlinisRepository;

class PenugasanKlinisService
{
    public function __construct(
        private readonly PenugasanKlinisRepository $penugasanKlinisRepository,
    ) {}

    public function getByUserId(int $userId): array
    {
        $pegawai = $this->penugasanKlinisRepository->findPegawaiByUserIdWithPenugasanKlinis($userId);

        $penugasanKlinisList = $pegawai?->penugasanKlinis
            ?->sortByDesc('tgl_mulai')
            ->values()
            ->map(function ($item) {
                return $this->formatPenugasanKlinisItem($item);
            });

        return [
            'label' => 'Riwayat Penugasan Klinis',
            'total' => $penugasanKlinisList ? $penugasanKlinisList->count() : 0,
            'items' => $penugasanKlinisList ? $penugasanKlinisList->toArray() : [],
        ];
    }

    public function createByUserId(int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $pegawai = $this->penugasanKlinisRepository->findPegawaiByUserIdWithPenugasanKlinis($userId);
        if ($pegawai === null) {
            throw new \InvalidArgumentException('Data pegawai tidak ditemukan.');
        }

        $skFilePath = null;
        if ($skFile !== null) {
            $folder = public_path('dokumen/penugasan-klinis');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $filename = sprintf(
                'sk-penugasan-klinis-%d-%d.%s',
                $pegawai->id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/penugasan-klinis/'.$filename;
        }

        $data = [
            'nomor_surat' => $payload['nomor_surat'],
            'tgl_mulai' => $payload['tgl_mulai'],
            'tgl_kadaluarsa' => $payload['tgl_kadaluarsa'] ?? null,
            'is_current' => (bool) $payload['is_current'],
            'dokumen_file_path' => $skFilePath,
        ];

        $penugasanKlinis = $this->penugasanKlinisRepository->createPenugasanKlinis($pegawai, $data);

        return $this->formatPenugasanKlinisItem($penugasanKlinis);
    }

    public function updateByIdAndUserId(int $id, int $userId, array $payload, ?\Illuminate\Http\UploadedFile $skFile = null): array
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $penugasanKlinis = $this->penugasanKlinisRepository->findPenugasanKlinisByIdAndUserId($id, $userId);
        if ($penugasanKlinis === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat penugasan klinis tidak ditemukan.');
        }

        $skFilePath = $penugasanKlinis->dokumen_file_path;
        if ($skFile !== null) {
            $folder = public_path('dokumen/penugasan-klinis');
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            if ($skFilePath && file_exists(public_path($skFilePath))) {
                @unlink(public_path($skFilePath));
            }

            $filename = sprintf(
                'sk-penugasan-klinis-%d-%d.%s',
                $penugasanKlinis->pegawai_id,
                time(),
                $skFile->getClientOriginalExtension()
            );

            $skFile->move($folder, $filename);
            $skFilePath = 'dokumen/penugasan-klinis/'.$filename;
        }

        $data = [];
        if (array_key_exists('nomor_surat', $payload)) $data['nomor_surat'] = $payload['nomor_surat'];
        if (array_key_exists('tgl_mulai', $payload)) $data['tgl_mulai'] = $payload['tgl_mulai'];
        if (array_key_exists('tgl_kadaluarsa', $payload)) $data['tgl_kadaluarsa'] = $payload['tgl_kadaluarsa'];
        if (array_key_exists('is_current', $payload)) $data['is_current'] = (bool) $payload['is_current'];
        if ($skFile !== null) $data['dokumen_file_path'] = $skFilePath;

        $updatedPenugasanKlinis = $this->penugasanKlinisRepository->updatePenugasanKlinis($penugasanKlinis, $data);

        return $this->formatPenugasanKlinisItem($updatedPenugasanKlinis);
    }

    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        if ($userId <= 0) {
            throw new \InvalidArgumentException('User login tidak valid.');
        }

        $penugasanKlinis = $this->penugasanKlinisRepository->findPenugasanKlinisByIdAndUserId($id, $userId);
        if ($penugasanKlinis === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Data riwayat penugasan klinis tidak ditemukan.');
        }

        $skFilePath = $penugasanKlinis->dokumen_file_path;
        if ($skFilePath && file_exists(public_path($skFilePath))) {
            @unlink(public_path($skFilePath));
        }

        $this->penugasanKlinisRepository->deletePenugasanKlinis($penugasanKlinis);
    }

    private function formatPenugasanKlinisItem($item): array
    {
        return [
            'id' => $item->id,
            'nomor_surat' => $item->nomor_surat,
            'tgl_mulai' => $item->tgl_mulai?->format('Y-m-d'),
            'tgl_kadaluarsa' => $item->tgl_kadaluarsa?->format('Y-m-d'),
            'is_current' => (bool) $item->is_current,
            'link_dokumen' => $item->dokumen_file_path ? url('/'.$item->dokumen_file_path) : null,
        ];
    }
}
