<?php

namespace App\Services\Profile;

use App\Repositories\Profile\PegawaiProfileRepository;
use Illuminate\Support\Carbon;

class PegawaiService
{
    public function __construct(
        private readonly PegawaiProfileRepository $profileRepository,
    ) {
    }

    public function build(int $userId): array
    {
        return $this->buildForRole($userId, 'pegawai');
    }

    public function buildForRole(int $userId, string $role): array
    {
        $user = $this->profileRepository->findUserWithPegawaiProfileRelations($userId);

        $pegawai = $user?->pegawai;

        $currentProfesi = $pegawai?->profesiPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->profesiPegawai?->first();

        $currentJabatan = $pegawai?->jabatanPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->jabatanPegawai?->first();

        $currentPangkat = $pegawai?->pangkatPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->pangkatPegawai?->first();

        $currentGolonganRuang = $pegawai?->golonganRuangPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->golonganRuangPegawai?->first();

        $lastUpdate = collect([
            $pegawai?->updated_at,
            $pegawai?->pribadi?->updated_at,
            $currentProfesi?->updated_at,
            $currentJabatan?->updated_at,
            $currentPangkat?->updated_at,
            $currentGolonganRuang?->updated_at,
        ])->filter()->max();

        $latestChangeRequest = $this->profileRepository->findLatestProfileChangeRequestByUserId($userId);

        return [
            'welcome' => 'Selamat datang '.$role,
            'summary' => [
                'label' => 'Profile '.$role,
                'nip' => $this->stringOrNull($pegawai?->nip),
                'nik' => $this->stringOrNull($pegawai?->nik),
                'nama' => $this->stringOrNull($pegawai?->nama),
                'jenis_pegawai' => $this->stringOrNull($pegawai?->jenisPegawai?->nama),
                'profesi' => $this->stringOrNull($currentProfesi?->profesi?->nama ?? $pegawai?->profesi?->nama),
                'pendidikan_terakhir' => $this->stringOrNull($pegawai?->pribadi?->pendidikan_terakhir),
                'unit_kerja' => $this->stringOrNull($currentJabatan?->jabatan?->unitKerja?->nama ?? $pegawai?->jabatan?->unitKerja?->nama),
                'jk' => $this->stringOrNull($pegawai?->pribadi?->jenis_kelamin),
                'tanggal_lahir' => optional($pegawai?->pribadi?->tanggal_lahir)?->toDateString(),
                'jabatan_sekarang' => $this->stringOrNull($currentJabatan?->jabatan?->nama ?? $pegawai?->jabatan?->nama),
                'agama' => $this->stringOrNull($pegawai?->pribadi?->agama),
                'status_kawin' => $this->stringOrNull($pegawai?->pribadi?->status_perkawinan),
                'alamat' => $this->stringOrNull($pegawai?->pribadi?->alamat),
                'no_telp' => $this->stringOrNull($pegawai?->pribadi?->no_telp),
                'email' => $this->stringOrNull($pegawai?->pribadi?->email),
                'no_kk' => $this->stringOrNull($pegawai?->pribadi?->no_kk),
                'link_kk' => $this->stringOrNull($pegawai?->pribadi?->link_kk),
                'link_photo_profile' => $this->buildPhotoProfileUrl((string) ($pegawai?->pribadi?->foto_path ?? '')),
                'ktp_file_path' => $this->stringOrNull($pegawai?->pribadi?->ktp_file_path),
                'status_pegawai' => $this->stringOrNull($pegawai?->status_pegawai),
                'tgl_masuk' => optional($pegawai?->tgl_masuk)?->toDateString(),
                'pangkat' => $this->stringOrNull($currentPangkat?->pangkat?->nama ?? $pegawai?->pangkat?->nama),
                'golongan_ruang' => $this->stringOrNull($currentGolonganRuang?->golonganRuang?->nama ?? $pegawai?->golonganRuang?->nama),
                'tmt_cpns' => optional($pegawai?->tmt_cpns)?->toDateString(),
                'tmt_pns' => optional($pegawai?->tmt_pns)?->toDateString(),
                'tmt_pangkat' => optional($currentPangkat?->started_at ?? $pegawai?->tmt_pangkat_akhir)?->toDateString(),
                'masa_kerja' => $this->calculateMasaKerja($pegawai?->tgl_masuk),
                'status_perubahan' => [
                    'fitur' => $this->stringOrNull($latestChangeRequest?->fitur),
                    'status' => $this->stringOrNull($latestChangeRequest?->status),
                    'note' => $this->stringOrNull($latestChangeRequest?->note),
                    'last_update' => optional($lastUpdate)->toDateTimeString(),
                ],
            ],
        ];
    }

    private function calculateMasaKerja(mixed $tglMasuk): ?string
    {
        if (! $tglMasuk) {
            return null;
        }

        $start = Carbon::parse($tglMasuk)->startOfDay();
        $today = now()->startOfDay();

        if ($start->greaterThan($today)) {
            return '0 tahun 0 bulan';
        }

        $diff = $start->diff($today);

        return sprintf('%d tahun %d bulan', $diff->y, $diff->m);
    }

    private function buildPhotoProfileUrl(string $path): ?string
    {
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return '/'.$path;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
