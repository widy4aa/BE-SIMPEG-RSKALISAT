<?php

namespace App\Repositories\Generate;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CvRepository
{
    public function getPegawaiForCv(int $pegawaiId): ?object
    {
        $row = DB::selectOne('
            SELECT
                p.*,
                u.username AS user_username,
                pp.id AS pribadi_id,
                pp.alamat AS pribadi_alamat,
                pp.no_telp AS pribadi_no_telp,
                pp.email AS pribadi_email,
                pp.tanggal_lahir AS pribadi_tanggal_lahir,
                pp.jenis_kelamin AS pribadi_jenis_kelamin,
                jp.nama AS jenis_pegawai_nama,
                pr.nama AS profesi_nama,
                j.nama AS jabatan_nama,
                uk.nama AS unit_kerja_nama,
                pk.nama AS pangkat_nama,
                gr.nama AS golongan_ruang_nama
            FROM pegawai p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN pegawai_pribadi pp ON pp.pegawai_id = p.id AND pp.deleted_at IS NULL
            LEFT JOIN jenis_pegawai jp ON jp.id = p.jenis_pegawai_id
            LEFT JOIN profesi pr ON pr.id = p.profesi_id
            LEFT JOIN jabatan j ON j.id = p.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            LEFT JOIN pangkat pk ON pk.id = p.pangkat_id AND pk.deleted_at IS NULL
            LEFT JOIN golongan_ruang gr ON gr.id = p.golongan_ruang_id
            WHERE p.id = ?
                AND p.deleted_at IS NULL
            LIMIT 1
        ', [$pegawaiId]);

        if ($row === null) {
            return null;
        }

        $pegawai = $this->mapPegawaiRow($row);
        $pribadiId = $pegawai->pribadi?->id;

        if ($pegawai->pribadi !== null) {
            $pegawai->pribadi->pendidikan = $this->getPendidikanByPribadiId((int) $pribadiId);
        }

        $pegawai->jadwalDiklat = $this->getJadwalDiklatByPegawaiId($pegawaiId);
        $pegawai->jabatanPegawai = $this->getJabatanPegawaiByPegawaiId($pegawaiId);
        $pegawai->str = $this->getSimpleRows('str', 'pegawai_id', $pegawaiId, ['tanggal_terbit', 'tanggal_kadaluarsa']);
        $pegawai->sip = $this->getSipByPegawaiId($pegawaiId);
        $pegawai->penugasanKlinis = $this->getSimpleRows('penugasan_klinis', 'pegawai_id', $pegawaiId, ['tgl_mulai', 'tgl_kadaluarsa']);

        return $pegawai;
    }

    public function getPegawaiIdByUserId(int $userId): ?int
    {
        $row = DB::selectOne('
            SELECT id
            FROM pegawai
            WHERE user_id = ?
                AND deleted_at IS NULL
            LIMIT 1
        ', [$userId]);

        return $row ? (int) $row->id : null;
    }

    private function mapPegawaiRow(object $row): object
    {
        $pegawai = (object) [
            'id' => (int) $row->id,
            'user_id' => $row->user_id !== null ? (int) $row->user_id : null,
            'nik' => $row->nik,
            'nip' => $row->nip,
            'nama' => $row->nama,
            'tgl_masuk' => $this->dateOrNull($row->tgl_masuk ?? null),
            'tmt_pns' => $this->dateOrNull($row->tmt_pns ?? null),
            'user' => (object) ['username' => $row->user_username ?? null],
            'jenisPegawai' => (object) ['nama' => $row->jenis_pegawai_nama ?? null],
            'profesi' => (object) ['nama' => $row->profesi_nama ?? null],
            'jabatan' => (object) [
                'nama' => $row->jabatan_nama ?? null,
                'unitKerja' => (object) ['nama' => $row->unit_kerja_nama ?? null],
            ],
            'pangkat' => (object) ['nama' => $row->pangkat_nama ?? null],
            'golonganRuang' => (object) ['nama' => $row->golongan_ruang_nama ?? null],
        ];

        $pegawai->pribadi = ($row->pribadi_id ?? null) === null
            ? null
            : (object) [
                'id' => (int) $row->pribadi_id,
                'alamat' => $row->pribadi_alamat ?? null,
                'no_telp' => $row->pribadi_no_telp ?? null,
                'no_hp' => null,
                'email' => $row->pribadi_email ?? null,
                'tanggal_lahir' => $this->dateOrNull($row->pribadi_tanggal_lahir ?? null),
                'jenis_kelamin' => $row->pribadi_jenis_kelamin ?? null,
                'pendidikan' => collect(),
            ];

        return $pegawai;
    }

    private function getPendidikanByPribadiId(int $pribadiId): Collection
    {
        return collect(DB::select('
            SELECT *
            FROM pendidikan
            WHERE pegawai_pribadi_id = ?
                AND deleted_at IS NULL
            ORDER BY tahun_lulus DESC, id DESC
        ', [$pribadiId]));
    }

    private function getJadwalDiklatByPegawaiId(int $pegawaiId): Collection
    {
        return collect(DB::select('
            SELECT
                ljd.*,
                d.nama_kegiatan,
                d.penyelenggara,
                d.tanggal_mulai,
                d.tanggal_selesai,
                d.jp,
                kd.nama AS kategori_diklat_nama
            FROM list_jadwal_diklat ljd
            INNER JOIN diklat d ON d.id = ljd.diklat_id AND d.deleted_at IS NULL
            LEFT JOIN kategori_diklat kd ON kd.id = d.kategori_diklat_id
            WHERE ljd.pegawai_id = ?
                AND ljd.deleted_at IS NULL
            ORDER BY d.tanggal_mulai DESC, ljd.id DESC
        ', [$pegawaiId]))->map(function ($row) {
            $row->diklat = (object) [
                'nama_kegiatan' => $row->nama_kegiatan ?? null,
                'penyelenggara' => $row->penyelenggara ?? null,
                'tanggal_mulai' => $this->dateOrNull($row->tanggal_mulai ?? null),
                'tanggal_selesai' => $this->dateOrNull($row->tanggal_selesai ?? null),
                'jp' => $row->jp ?? null,
                'kategoriDiklat' => (object) ['nama' => $row->kategori_diklat_nama ?? null],
            ];

            return $row;
        });
    }

    private function getJabatanPegawaiByPegawaiId(int $pegawaiId): Collection
    {
        return collect(DB::select('
            SELECT
                jp.*,
                j.nama AS jabatan_nama,
                uk.nama AS unit_kerja_nama
            FROM jabatan_pegawai jp
            LEFT JOIN jabatan j ON j.id = jp.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            WHERE jp.pegawai_id = ?
                AND jp.deleted_at IS NULL
            ORDER BY jp.started_at DESC, jp.id DESC
        ', [$pegawaiId]))->map(function ($row) {
            $row->started_at = $this->dateOrNull($row->started_at ?? null);
            $row->ended_at = $this->dateOrNull($row->ended_at ?? null);
            $row->jabatan = (object) [
                'nama' => $row->jabatan_nama ?? null,
                'unitKerja' => (object) ['nama' => $row->unit_kerja_nama ?? null],
            ];

            return $row;
        });
    }

    private function getSipByPegawaiId(int $pegawaiId): Collection
    {
        return collect(DB::select('
            SELECT
                s.*,
                js.nama AS jenis_sip_nama
            FROM sip s
            LEFT JOIN jenis_sip js ON js.id = s.jenis_sip_id
            WHERE s.pegawai_id = ?
                AND s.deleted_at IS NULL
            ORDER BY s.tanggal_kadaluarsa DESC, s.id DESC
        ', [$pegawaiId]))->map(function ($row) {
            $row->tanggal_terbit = $this->dateOrNull($row->tanggal_terbit ?? null);
            $row->tanggal_kadaluarsa = $this->dateOrNull($row->tanggal_kadaluarsa ?? null);
            $row->jenisSip = (object) ['nama' => $row->jenis_sip_nama ?? null];

            return $row;
        });
    }

    private function getSimpleRows(string $table, string $key, int $value, array $dateFields = []): Collection
    {
        return collect(DB::select("
            SELECT *
            FROM {$table}
            WHERE {$key} = ?
                AND deleted_at IS NULL
            ORDER BY id DESC
        ", [$value]))->map(function ($row) use ($dateFields) {
            foreach ($dateFields as $field) {
                $row->{$field} = $this->dateOrNull($row->{$field} ?? null);
            }

            return $row;
        });
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value)->startOfDay() : null;
    }
}
