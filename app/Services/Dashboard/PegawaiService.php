<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\PegawaiDashboardRepository;
use App\Models\StrPegawai;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PegawaiService
{
    public function __construct(private readonly PegawaiDashboardRepository $pegawaiDashboardRepository)
    {
    }

    public function build(int $userId): array
    {
        $pegawai = $this->pegawaiDashboardRepository->findPegawaiDashboardByUserId($userId);

        $unitKerjaAktif = $pegawai?->unitKerjaPegawai
            ?->firstWhere('is_current', true)
            ?? $pegawai?->unitKerjaPegawai?->first();

        $strTerbaru = $pegawai?->strs->first();
        $statusStr = $this->cekMasaBerlakuStr($strTerbaru);
        $statusKeluarga = $this->cekStatusDataKeluarga(
            keluarga: $pegawai?->pribadi?->keluarga ?? collect(),
            bukuNikahFilePath: (string) ($pegawai?->pribadi?->buku_nikah_file_path ?? '')
        );
        $listNotifikasi = $this->pegawaiDashboardRepository
            ->getUnreadNotificationsByUserId($userId)
            ->map(function ($notification) {
                return [
                    'id' => (int) $notification->id,
                    'title' => (string) ($notification->title ?? ''),
                    'message' => (string) ($notification->message ?? ''),
                    'is_read' => (bool) $notification->is_read,
                    'created_at' => optional($notification->created_at)?->toDateTimeString(),
                ];
            })
            ->values()
            ->all();
        $listJadwalDiklatMendatang = $this->pegawaiDashboardRepository
            ->getUpcomingDiklatByPegawaiId((int) ($pegawai?->id ?? 0))
            ->map(function ($jadwal) {
                return [
                    'jadwal_id' => (int) $jadwal->id,
                    'status_diklat' => (string) $jadwal->status_diklat,
                    'nama_kegiatan' => (string) ($jadwal->diklat?->nama_kegiatan ?? ''),
                    'penyelenggara' => (string) ($jadwal->diklat?->penyelenggara ?? ''),
                    'tanggal_mulai' => optional($jadwal->diklat?->tanggal_mulai)?->toDateString(),
                    'tanggal_selesai' => optional($jadwal->diklat?->tanggal_selesai)?->toDateString(),
                    'tempat' => (string) ($jadwal->diklat?->tempat ?? ''),
                    'waktu' => optional($jadwal->diklat?->waktu)?->format('H:i:s'),
                ];
            })
            ->values()
            ->all() ?? [];

        return [
            'welcome' => 'Selamat datang pegawai',
            'summary' => [
                'label' => 'Dashboard pegawai',
                'nama' => (string) ($pegawai?->nama ?? ''),
                'nip' => (string) ($pegawai?->nip ?? ''),
                'jabatan' => (string) ($pegawai?->jabatan?->nama ?? ''),
                'jenis_jabatan' => (string) ($pegawai?->jenisPegawai?->nama ?? ''),
                'unit_kerja' => (string) ($unitKerjaAktif?->unitKerja?->nama ?? ''),
                'jumlah_diklat_selesai' => (int) ($pegawai?->jumlah_diklat_selesai ?? 0),
                'jumlah_diklat_dijadwalkan_belum_selesai' => (int) ($pegawai?->jumlah_diklat_belum_selesai ?? 0),
                'list_jadwal_diklat_mendatang' => $listJadwalDiklatMendatang,
                'list_notifikasi' => $listNotifikasi,
                'list_aksi' => [
                    'status_str' => $statusStr,
                    'status_data_keluarga' => $statusKeluarga,
                ],
            ],
        ];
    }

    private function cekMasaBerlakuStr(?StrPegawai $str): array
    {
        if ($str === null || $str->tanggal_kadaluarsa === null) {
            return [
                'status_lengkap' => false,
                'sisa_hari' => null,
                'keterangan' => ['STR belum tersedia'],
            ];
        }

        $hariIni = Carbon::today();
        $tanggalKadaluarsa = Carbon::parse($str->tanggal_kadaluarsa)->startOfDay();
        $sisaHari = $hariIni->diffInDays($tanggalKadaluarsa, false);

        return [
            'status_lengkap' => true,
            'sisa_hari' => $sisaHari,
            'keterangan' => $sisaHari >= 0
                ? ['STR aktif']
                : ['STR sudah kadaluarsa'],
        ];
    }

    private function cekStatusDataKeluarga(Collection $keluarga, string $bukuNikahFilePath): array
    {
        $keterangan = [];
        $isLengkap = true;

        if ($bukuNikahFilePath === '') {
            $isLengkap = false;
            $keterangan[] = 'bukti pernikahan belum ada';
        }

        if ($keluarga->isEmpty()) {
            $isLengkap = false;
            $keterangan[] = 'data keluarga belum ada';
        }

        foreach ($keluarga as $anggotaKeluarga) {
            $isItemLengkap = filled($anggotaKeluarga->nama)
                && filled($anggotaKeluarga->hubungan)
                && filled($anggotaKeluarga->tanggal_lahir)
                && filled($anggotaKeluarga->pekerjaan);

            if (! $isItemLengkap) {
                $isLengkap = false;
                $keterangan[] = (string) ($anggotaKeluarga->nama ?: 'nama keluarga kosong');
            }
        }

        $keterangan = array_values(array_unique($keterangan));

        return [
            'status_lengkap' => $isLengkap,
            'keterangan' => $isLengkap ? ['data lengkap'] : $keterangan,
        ];
    }
}
