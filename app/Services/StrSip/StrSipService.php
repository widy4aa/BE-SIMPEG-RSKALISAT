<?php

namespace App\Services\StrSip;

use App\Repositories\StrSip\StrSipRepository;
use Carbon\Carbon;

class StrSipService
{
    private const HAMPIR_HABIS_DAYS = 30;

    public function __construct(private readonly StrSipRepository $repository)
    {
    }

    public function getSummary(): array
    {
        $items = $this->buildItems();
        $summary = $this->buildSummary($items);

        return [
            'summary' => $summary,
            'items' => $items,
        ];
    }

    private function buildItems(): array
    {
        $items = [];

        foreach ($this->repository->getAllStr() as $str) {
            $tanggalHabis = $str->tanggal_kadaluarsa;

            $items[] = [
                'id' => (int) $str->id,
                'pegawai_id' => (int) $str->pegawai_id,
                'nama' => (string) ($str->pegawai?->nama ?? ''),
                'nip' => (string) ($str->pegawai?->nip ?? ''),
                'profesi' => (string) ($str->pegawai?->profesi?->nama ?? ''),
                'str_sip' => 'STR',
                'jenis' => null,
                'nomor' => $str->nomor_str,
                'link_pdf' => $this->resolveDokumenUrl($str->sk_file_path),
                'tanggal_terbit' => $str->tanggal_terbit?->toDateString(),
                'tanggal_selesai' => $tanggalHabis?->toDateString(),
                'status' => $this->resolveStatus($tanggalHabis),
                'is_current' => (bool) $str->is_current,
            ];
        }

        foreach ($this->repository->getAllSip() as $sip) {
            $tanggalHabis = $sip->tanggal_kadaluarsa;

            $items[] = [
                'id' => (int) $sip->id,
                'pegawai_id' => (int) $sip->pegawai_id,
                'nama' => (string) ($sip->pegawai?->nama ?? ''),
                'nip' => (string) ($sip->pegawai?->nip ?? ''),
                'profesi' => (string) ($sip->pegawai?->profesi?->nama ?? ''),
                'str_sip' => 'SIP',
                'jenis' => $sip->jenisSip?->nama,
                'nomor' => $sip->nomor_sip,
                'link_pdf' => $this->resolveDokumenUrl($sip->sk_file_path),
                'tanggal_terbit' => $sip->tanggal_terbit?->toDateString(),
                'tanggal_selesai' => $tanggalHabis?->toDateString(),
                'status' => $this->resolveStatus($tanggalHabis),
                'is_current' => (bool) $sip->is_current,
            ];
        }

        return collect($items)
            ->sortBy(function (array $item) {
                return $item['tanggal_selesai'] ?? '9999-12-31';
            })
            ->values()
            ->all();
    }

    private function buildSummary(array $items): array
    {
        $total = count($items);
        $aktif = 0;
        $hampirHabis = 0;
        $tidakAktif = 0;

        foreach ($items as $item) {
            if ($item['status'] === 'Aktif') {
                $aktif++;
                continue;
            }

            if ($item['status'] === 'Hampir Habis') {
                $hampirHabis++;
                continue;
            }

            $tidakAktif++;
        }

        return [
            'total' => $total,
            'aktif' => $aktif,
            'hampir_habis' => $hampirHabis,
            'tidak_aktif' => $tidakAktif,
        ];
    }

    private function resolveStatus(?Carbon $tanggalHabis): string
    {
        if ($tanggalHabis === null) {
            return 'Tidak Aktif';
        }

        $today = Carbon::today();

        if ($tanggalHabis->lt($today)) {
            return 'Tidak Aktif';
        }

        $sisaHari = $today->diffInDays($tanggalHabis, false);

        if ($sisaHari <= self::HAMPIR_HABIS_DAYS) {
            return 'Hampir Habis';
        }

        return 'Aktif';
    }

    private function resolveDokumenUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return '/'.$path;
    }
}
