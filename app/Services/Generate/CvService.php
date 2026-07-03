<?php

namespace App\Services\Generate;

use App\Repositories\Generate\CvRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CvService
{
    public function __construct(
        private readonly CvRepository $cvRepository
    ) {
    }

    public function generateCvData(int $userId, string $role, ?int $requestedPegawaiId = null): array
    {
        $pegawaiId = null;

        if ($requestedPegawaiId !== null && in_array($role, ['admin', 'hrd', 'direktur'], true)) {
            $pegawaiId = $requestedPegawaiId;
        } else {
            $pegawaiId = $this->cvRepository->getPegawaiIdByUserId($userId);
        }

        if (!$pegawaiId) {
            throw new ModelNotFoundException('Data pegawai tidak ditemukan.');
        }

        $pegawai = $this->cvRepository->getPegawaiForCv($pegawaiId);
        if (!$pegawai) {
            throw new ModelNotFoundException('Data pegawai tidak ditemukan.');
        }

        $pribadi = $pegawai->pribadi;
        $user = $pegawai->user;

        $masaKerja = '-';
        if ($pegawai->tgl_masuk) {
            $tglMasuk = Carbon::parse($pegawai->tgl_masuk);
            $now = Carbon::now();
            $diff = $tglMasuk->diff($now);
            $masaKerja = $diff->y . ' tahun ' . $diff->m . ' bulan';
        }

        $pendidikanList = [];
        if ($pribadi && $pribadi->pendidikan) {
            foreach ($pribadi->pendidikan as $p) {
                $pendidikanList[] = [
                    'jenjang' => $p->jenjang ?? '-',
                    'jurusan' => $p->jurusan ?? '-',
                    'institusi' => $p->institusi ?? '-',
                    'tahun_lulus' => $p->tahun_lulus ?? '-',
                ];
            }
        }

        $diklatList = [];
        if ($pegawai->jadwalDiklat) {
            foreach ($pegawai->jadwalDiklat as $jd) {
                $diklat = $jd->diklat;
                if ($diklat) {
                    $diklatList[] = [
                        'nama' => $diklat->nama_kegiatan ?? '-',
                        'jenis' => $diklat->kategoriDiklat?->nama ?? '-',
                        'pelaksana' => $diklat->penyelenggara ?? '-',
                        'tanggal_mulai' => $diklat->tanggal_mulai ? $diklat->tanggal_mulai->format('Y-m-d') : '-',
                        'tanggal_selesai' => $diklat->tanggal_selesai ? $diklat->tanggal_selesai->format('Y-m-d') : '-',
                        'jp' => $diklat->jp ?? '-',
                        'no_sertif' => $jd->no_sertif ?? '-',
                        'link_sertifikat' => $jd->sertif_file_path ? url('storage/' . $jd->sertif_file_path) : '-',
                    ];
                }
            }
        }

        $riwayatJabatanList = [];
        if ($pegawai->jabatanPegawai) {
            foreach ($pegawai->jabatanPegawai as $jp) {
                $riwayatJabatanList[] = [
                    'jabatan' => $jp->jabatan?->nama ?? '-',
                    'unit_kerja' => $jp->jabatan?->unitKerja?->nama ?? '-',
                    'tanggal_mulai' => $jp->started_at ? Carbon::parse($jp->started_at)->format('Y-m-d') : '-',
                    'tanggal_selesai' => $jp->ended_at ? Carbon::parse($jp->ended_at)->format('Y-m-d') : '-',
                    'is_current' => (bool) $jp->is_current,
                    'catatan' => $jp->note ?? '-',
                ];
            }
        }

        $strList = [];
        if ($pegawai->str) {
            foreach ($pegawai->str as $s) {
                $strList[] = [
                    'nomor_str' => $s->nomor_str ?? '-',
                    'tanggal_terbit' => $s->tanggal_terbit ? Carbon::parse($s->tanggal_terbit)->format('Y-m-d') : '-',
                    'tanggal_kadaluarsa' => $s->tanggal_kadaluarsa ? Carbon::parse($s->tanggal_kadaluarsa)->format('Y-m-d') : '-',
                    'is_current' => (bool) $s->is_current,
                ];
            }
        }

        $sipList = [];
        if ($pegawai->sip) {
            foreach ($pegawai->sip as $s) {
                $sipList[] = [
                    'jenis_sip' => $s->jenisSip?->nama ?? '-',
                    'nomor_sip' => $s->nomor_sip ?? '-',
                    'tanggal_terbit' => $s->tanggal_terbit ? Carbon::parse($s->tanggal_terbit)->format('Y-m-d') : '-',
                    'tanggal_kadaluarsa' => $s->tanggal_kadaluarsa ? Carbon::parse($s->tanggal_kadaluarsa)->format('Y-m-d') : '-',
                    'is_current' => (bool) $s->is_current,
                ];
            }
        }

        $penugasanKlinisList = [];
        if ($pegawai->penugasanKlinis) {
            foreach ($pegawai->penugasanKlinis as $pk) {
                $penugasanKlinisList[] = [
                    'nomor_surat' => $pk->nomor_surat ?? '-',
                    'tanggal_mulai' => $pk->tgl_mulai ? Carbon::parse($pk->tgl_mulai)->format('Y-m-d') : '-',
                    'tanggal_kadaluarsa' => $pk->tgl_kadaluarsa ? Carbon::parse($pk->tgl_kadaluarsa)->format('Y-m-d') : '-',
                    'is_current' => (bool) $pk->is_current,
                ];
            }
        }

        return [
            'header' => [
                'nama' => $pegawai->nama ?? '-',
                'alamat' => $pribadi?->alamat ?? '-',
                'no_telp' => $pribadi?->no_hp ?? $pribadi?->no_telp ?? '-',
                'email' => $user?->email ?? '-',
            ],
            'profil' => [
                'jabatan' => $pegawai->jabatan?->nama ?? '-',
                'profesi' => $pegawai->profesi?->nama ?? '-',
                'unit_kerja' => $pegawai->jabatan?->unitKerja?->nama ?? '-',
                'masa_kerja' => $masaKerja,
            ],
            'data_diri' => [
                'nip' => $pegawai->nip ?? '-',
                'nik' => $pegawai->nik ?? '-',
                'tanggal_lahir' => $pribadi?->tanggal_lahir ? $pribadi->tanggal_lahir->format('Y-m-d') : '-',
                'jenis_kelamin' => $pribadi?->jenis_kelamin ?? '-',
                'golongan_ruang' => $pegawai->golonganRuang?->nama ?? '-',
                'pangkat' => $pegawai->pangkat?->nama ?? '-',
                'jabatan' => $pegawai->jabatan?->nama ?? '-',
                'unit_kerja' => $pegawai->jabatan?->unitKerja?->nama ?? '-',
                'tmt_pns' => $pegawai->tmt_pns ? $pegawai->tmt_pns->format('Y-m-d') : '-',
                'status_kepegawaian' => $pegawai->jenisPegawai?->nama ?? '-',
            ],
            'pendidikan' => $pendidikanList,
            'diklat' => $diklatList,
            'riwayat_jabatan' => $riwayatJabatanList,
            'str' => $strList,
            'sip' => $sipList,
            'penugasan_klinis' => $penugasanKlinisList,
            'ttd' => [
                'kota' => 'Kalisat',
                'tanggal' => Carbon::now()->format('Y-m-d'),
            ],
        ];
    }
}
