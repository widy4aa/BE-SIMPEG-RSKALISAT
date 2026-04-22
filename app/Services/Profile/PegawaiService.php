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
        $user = $this->profileRepository->findUserWithPegawaiProfileRelations($userId);

        $pegawai = $user?->pegawai;

        $currentProfesi = $pegawai?->profesiPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->profesiPegawai?->first();

        $currentJabatan = $pegawai?->jabatanPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->jabatanPegawai?->first();

        $currentUnitKerja = $pegawai?->unitKerjaPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->unitKerjaPegawai?->first();

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
            $currentUnitKerja?->updated_at,
            $currentPangkat?->updated_at,
            $currentGolonganRuang?->updated_at,
        ])->filter()->max();

        $latestChangeRequest = $this->profileRepository->findLatestProfileChangeRequestByUserId($userId);

        return [
            'welcome' => 'Selamat datang pegawai',
            'summary' => [
                'label' => 'Profile pegawai',
                'nip' => (string) ($pegawai?->nip ?? ''),
                'nik' => (string) ($pegawai?->nik ?? ''),
                'nama' => (string) ($pegawai?->nama ?? ''),
                'jenis_pegawai' => (string) ($pegawai?->jenisPegawai?->nama ?? ''),
                'profesi' => (string) ($currentProfesi?->profesi?->nama ?? $pegawai?->profesi?->nama ?? ''),
                'pendidikan_terakhir' => (string) ($pegawai?->pribadi?->pendidikan_terakhir ?? ''),
                'unit_kerja' => (string) ($currentUnitKerja?->unitKerja?->nama ?? ''),
                'jk' => (string) ($pegawai?->pribadi?->jenis_kelamin ?? ''),
                'tanggal_lahir' => optional($pegawai?->pribadi?->tanggal_lahir)?->toDateString(),
                'jabatan_sekarang' => (string) ($currentJabatan?->jabatan?->nama ?? $pegawai?->jabatan?->nama ?? ''),
                'agama' => (string) ($pegawai?->pribadi?->agama ?? ''),
                'status_kawin' => (string) ($pegawai?->pribadi?->status_perkawinan ?? ''),
                'alamat' => (string) ($pegawai?->pribadi?->alamat ?? ''),
                'no_telp' => (string) ($pegawai?->pribadi?->no_telp ?? ''),
                'email' => (string) ($pegawai?->pribadi?->email ?? ''),
                'no_kk' => (string) ($pegawai?->pribadi?->no_kk ?? ''),
                'link_kk' => (string) ($pegawai?->pribadi?->link_kk ?? ''),
                'link_photo_profile' => $this->buildPhotoProfileUrl((string) ($pegawai?->pribadi?->foto_path ?? '')),
                'ktp_file_path' => (string) ($pegawai?->pribadi?->ktp_file_path ?? ''),
                'status_pegawai' => (string) ($pegawai?->status_pegawai ?? ''),
                'tgl_masuk' => optional($pegawai?->tgl_masuk)?->toDateString(),
                'pangkat' => (string) ($currentPangkat?->pangkat?->nama ?? $pegawai?->pangkat?->nama ?? ''),
                'golongan_ruang' => (string) ($currentGolonganRuang?->golonganRuang?->nama ?? $pegawai?->golonganRuang?->nama ?? ''),
                'tmt_cpns' => optional($pegawai?->tmt_cpns)?->toDateString(),
                'tmt_pns' => optional($pegawai?->tmt_pns)?->toDateString(),
                'tmt_pangkat' => optional($currentPangkat?->started_at ?? $pegawai?->tmt_pangkat_akhir)?->toDateString(),
                'masa_kerja' => $this->calculateMasaKerja($pegawai?->tgl_masuk),
                'status_perubahan' => [
                    'fitur' => (string) ($latestChangeRequest?->fitur ?? ''),
                    'status' => (string) ($latestChangeRequest?->status ?? ''),
                    'note' => (string) ($latestChangeRequest?->note ?? ''),
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

        return url('/'.$path);
    }
}
