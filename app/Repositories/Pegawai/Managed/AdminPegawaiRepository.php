<?php

namespace App\Repositories\Pegawai\Managed;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPegawaiRepository
{
    public function getAllPegawai(): Collection
    {
        return collect(DB::select('
            SELECT
                p.*,
                u.role AS user_role
            FROM pegawai p
            LEFT JOIN users u ON u.id = p.user_id
            WHERE p.deleted_at IS NULL
            ORDER BY p.id DESC
        '))->map(fn ($row) => $this->mapPegawaiListRow($row));
    }

    public function getPegawaiOverviewCounts(): array
    {
        $row = DB::selectOne('
            SELECT
                COUNT(*) AS total_pegawai,
                SUM(CASE WHEN pr.nama LIKE ? THEN 1 ELSE 0 END) AS jumlah_dokter,
                SUM(CASE WHEN pr.nama LIKE ? THEN 1 ELSE 0 END) AS jumlah_perawat,
                COUNT(DISTINCT p.profesi_id) AS jumlah_profesi
            FROM pegawai p
            LEFT JOIN profesi pr ON pr.id = p.profesi_id
            WHERE p.deleted_at IS NULL
        ', ['%dokter%', '%perawat%']);

        return [
            'total_pegawai' => (int) ($row->total_pegawai ?? 0),
            'jumlah_dokter' => (int) ($row->jumlah_dokter ?? 0),
            'jumlah_perawat' => (int) ($row->jumlah_perawat ?? 0),
            'jumlah_profesi' => (int) ($row->jumlah_profesi ?? 0),
        ];
    }

    public function getPaginatedPegawai(int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $offset = ($page - 1) * $perPage;
        [$whereSql, $bindings] = $this->buildPegawaiFilterSql($filters);

        $totalRow = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM pegawai p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN pegawai_pribadi pp ON pp.pegawai_id = p.id AND pp.deleted_at IS NULL
            LEFT JOIN jenis_pegawai jp ON jp.id = p.jenis_pegawai_id
            LEFT JOIN profesi pr ON pr.id = p.profesi_id
            LEFT JOIN jabatan j ON j.id = p.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            WHERE {$whereSql}
        ", $bindings);

        $rows = DB::select("
            SELECT
                p.*,
                u.role AS user_role,
                pp.id AS pribadi_id,
                pp.pendidikan_terakhir AS pribadi_pendidikan_terakhir,
                pp.tanggal_lahir AS pribadi_tanggal_lahir,
                pp.jenis_kelamin AS pribadi_jenis_kelamin,
                pp.agama AS pribadi_agama,
                pp.status_perkawinan AS pribadi_status_perkawinan,
                pp.alamat AS pribadi_alamat,
                pp.no_telp AS pribadi_no_telp,
                pp.email AS pribadi_email,
                pp.foto_path AS pribadi_foto_path,
                pp.ktp_file_path AS pribadi_ktp_file_path,
                pp.kk_file_path AS pribadi_kk_file_path,
                j.id AS jabatan_id_detail,
                j.nama AS jabatan_nama,
                uk.id AS unit_kerja_id,
                uk.nama AS unit_kerja_nama,
                jp.nama AS jenis_pegawai_nama,
                pr.nama AS profesi_nama
            FROM pegawai p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN pegawai_pribadi pp ON pp.pegawai_id = p.id AND pp.deleted_at IS NULL
            LEFT JOIN jenis_pegawai jp ON jp.id = p.jenis_pegawai_id
            LEFT JOIN profesi pr ON pr.id = p.profesi_id
            LEFT JOIN jabatan j ON j.id = p.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            WHERE {$whereSql}
            ORDER BY p.id DESC
            LIMIT ? OFFSET ?
        ", [...$bindings, $perPage, $offset]);

        return new LengthAwarePaginator(
            collect($rows)->map(fn ($row) => $this->mapPegawaiListRow($row)),
            (int) ($totalRow->total ?? 0),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]
        );
    }

    public function getPegawaiDetail(int $pegawaiId): ?object
    {
        $row = DB::selectOne('
            SELECT
                p.*,
                u.role AS user_role,
                u.username AS user_username,
                pp.id AS pribadi_id,
                pp.pendidikan_terakhir AS pribadi_pendidikan_terakhir,
                pp.no_kk AS pribadi_no_kk,
                pp.tanggal_lahir AS pribadi_tanggal_lahir,
                pp.jenis_kelamin AS pribadi_jenis_kelamin,
                pp.agama AS pribadi_agama,
                pp.status_perkawinan AS pribadi_status_perkawinan,
                pp.alamat AS pribadi_alamat,
                pp.no_telp AS pribadi_no_telp,
                pp.email AS pribadi_email,
                pp.foto_path AS pribadi_foto_path,
                pp.ktp_file_path AS pribadi_ktp_file_path,
                pp.kk_file_path AS pribadi_kk_file_path,
                pr.nama AS profesi_nama,
                jp.nama AS jenis_pegawai_nama,
                j.nama AS jabatan_nama,
                uk.nama AS unit_kerja_nama,
                gr.nama AS golongan_ruang_nama,
                pk.nama AS pangkat_nama
            FROM pegawai p
            LEFT JOIN users u ON u.id = p.user_id
            LEFT JOIN pegawai_pribadi pp ON pp.pegawai_id = p.id AND pp.deleted_at IS NULL
            LEFT JOIN profesi pr ON pr.id = p.profesi_id
            LEFT JOIN jenis_pegawai jp ON jp.id = p.jenis_pegawai_id
            LEFT JOIN jabatan j ON j.id = p.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            LEFT JOIN golongan_ruang gr ON gr.id = p.golongan_ruang_id
            LEFT JOIN pangkat pk ON pk.id = p.pangkat_id AND pk.deleted_at IS NULL
            WHERE p.id = ?
                AND p.deleted_at IS NULL
            LIMIT 1
        ', [$pegawaiId]);

        if ($row === null) {
            return null;
        }

        $pegawai = $this->mapPegawaiDetailRow($row);
        $pribadiId = $pegawai->pribadi?->id;

        $pegawai->pasangan = $this->selectCollection('SELECT * FROM pasangan WHERE pegawai_pribadi_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pribadiId]);
        $pegawai->anak = $this->selectCollection('SELECT * FROM anak WHERE pegawai_pribadi_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pribadiId], ['tanggal_lahir']);
        $pegawai->orangTua = $this->selectCollection('SELECT * FROM orang_tua WHERE pegawai_pribadi_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pribadiId]);
        $pegawai->kontakDarurat = $this->selectCollection('SELECT * FROM kontak_darurat WHERE pegawai_pribadi_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pribadiId]);
        $pegawai->tanggunganLain = $this->selectCollection('SELECT * FROM tanggungan_lain WHERE pegawai_pribadi_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pribadiId]);
        $pegawai->pendidikan = $this->selectCollection('SELECT * FROM pendidikan WHERE pegawai_pribadi_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pribadiId]);
        $pegawai->str = $this->selectCollection('SELECT * FROM str WHERE pegawai_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pegawaiId], ['tanggal_terbit', 'tanggal_kadaluarsa']);
        $pegawai->sip = $this->getSipByPegawaiId($pegawaiId);
        $pegawai->penugasanKlinis = $this->selectCollection('SELECT * FROM penugasan_klinis WHERE pegawai_id = ? AND deleted_at IS NULL ORDER BY id DESC', [$pegawaiId], ['tgl_mulai', 'tgl_kadaluarsa']);
        $pegawai->jabatanPegawai = $this->getRiwayatJabatanByPegawaiId($pegawaiId);
        $pegawai->riwayatPangkat = $this->getRiwayatPangkatByPegawaiId($pegawaiId);
        $pegawai->jadwalDiklat = $this->getJadwalDiklatByPegawaiId($pegawaiId);

        return $pegawai;
    }

    public function createPegawaiData(array $data): object
    {
        return DB::transaction(function () use ($data) {
            $now = Carbon::now()->toDateTimeString();
            DB::insert('
                INSERT INTO users (username, password, role, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?)
            ', [$data['nik'], Hash::make($data['password']), 'pegawai', true, $now, $now]);
            $userId = (int) DB::getPdo()->lastInsertId();

            DB::insert('
                INSERT INTO pegawai (user_id, nik, nama, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?)
            ', [$userId, $data['nik'], $data['nama'], $now, $now]);
            $pegawaiId = (int) DB::getPdo()->lastInsertId();

            DB::insert('
                INSERT INTO pegawai_pribadi (pegawai_id, created_at, updated_at)
                VALUES (?, ?, ?)
            ', [$pegawaiId, $now, $now]);

            return (object) [
                'id' => $pegawaiId,
                'user_id' => $userId,
                'nik' => $data['nik'],
                'nama' => $data['nama'],
            ];
        });
    }

    public function changeRole(int $pegawaiId, array $data): object
    {
        $pegawai = DB::selectOne('
            SELECT p.*, u.role AS user_role
            FROM pegawai p
            LEFT JOIN users u ON u.id = p.user_id
            WHERE p.id = ?
                AND p.deleted_at IS NULL
            LIMIT 1
        ', [$pegawaiId]);

        if ($pegawai === null) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Pegawai tidak ditemukan.');
        }

        DB::transaction(function () use ($pegawai, $data): void {
            $now = Carbon::now()->toDateTimeString();

            if (isset($data['role']) && $pegawai->user_id !== null) {
                DB::update('
                    UPDATE users
                    SET role = ?, updated_at = ?
                    WHERE id = ?
                ', [$data['role'], $now, $pegawai->user_id]);
                $pegawai->user_role = $data['role'];
            }

            if (isset($data['status_pegawai'])) {
                DB::update('
                    UPDATE pegawai
                    SET status_pegawai = ?, updated_at = ?
                    WHERE id = ?
                ', [$data['status_pegawai'], $now, $pegawai->id]);
                $pegawai->status_pegawai = $data['status_pegawai'];
            }
        });

        $pegawai->user = (object) ['role' => $pegawai->user_role ?? null];

        return $pegawai;
    }

    public function countUsersByRole(string $role): int
    {
        $row = DB::selectOne('
            SELECT COUNT(*) AS total
            FROM users
            WHERE role = ?
        ', [$role]);

        return (int) ($row->total ?? 0);
    }

    public function getTotalPegawaiAktif(): int
    {
        $row = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM pegawai
            WHERE status_pegawai = 'aktif'
                AND deleted_at IS NULL
        ");

        return (int) ($row->total ?? 0);
    }

    private function buildPegawaiFilterSql(array $filters): array
    {
        $where = ['p.deleted_at IS NULL'];
        $bindings = [];

        $search = $this->filledString($filters['search'] ?? null);
        if ($search !== null) {
            $like = "%{$search}%";
            $where[] = '(
                p.nama LIKE ?
                OR p.nik LIKE ?
                OR pr.nama LIKE ?
            )';
            array_push($bindings, $like, $like, $like);
        }

        $statusKelengkapan = $this->filledString($filters['status_kelengkapan'] ?? null);
        if ($statusKelengkapan === 'lengkap') {
            $where[] = $this->profileCompleteSql();
        } elseif ($statusKelengkapan === 'belum-lengkap') {
            $where[] = 'NOT '.$this->profileCompleteSql();
        }

        $jenisPegawai = $this->filledString($filters['jenis_pegawai'] ?? null);
        if ($jenisPegawai !== null) {
            $where[] = 'jp.nama LIKE ?';
            $bindings[] = "%{$jenisPegawai}%";
        }

        $pendidikan = $this->filledString($filters['pendidikan'] ?? null);
        if ($pendidikan !== null) {
            $where[] = 'pp.pendidikan_terakhir LIKE ?';
            $bindings[] = "%{$pendidikan}%";
        }

        $statusPegawai = $this->filledString($filters['status_pegawai'] ?? null);
        if ($statusPegawai !== null) {
            $where[] = 'p.status_pegawai = ?';
            $bindings[] = $statusPegawai;
        }

        $profesi = $this->filledString($filters['profesi'] ?? null);
        if ($profesi !== null) {
            $where[] = 'pr.nama LIKE ?';
            $bindings[] = "%{$profesi}%";
        }

        $tahunMasuk = $this->filledString($filters['tahun_masuk'] ?? null);
        if ($tahunMasuk !== null && ctype_digit($tahunMasuk)) {
            $where[] = 'YEAR(p.tgl_masuk) = ?';
            $bindings[] = (int) $tahunMasuk;
        }

        $tglMasukDari = $this->filledString($filters['tgl_masuk_dari'] ?? null);
        if ($tglMasukDari !== null) {
            $where[] = 'DATE(p.tgl_masuk) >= ?';
            $bindings[] = $tglMasukDari;
        }

        $tglMasukSampai = $this->filledString($filters['tgl_masuk_sampai'] ?? null);
        if ($tglMasukSampai !== null) {
            $where[] = 'DATE(p.tgl_masuk) <= ?';
            $bindings[] = $tglMasukSampai;
        }

        return [implode(' AND ', $where), $bindings];
    }

    private function profileCompleteSql(): string
    {
        return "(
            pp.id IS NOT NULL
            AND pp.tanggal_lahir IS NOT NULL
            AND pp.jenis_kelamin IS NOT NULL
            AND pp.jenis_kelamin <> ''
            AND pp.agama IS NOT NULL
            AND pp.agama <> ''
            AND pp.alamat IS NOT NULL
            AND pp.alamat <> ''
            AND pp.no_telp IS NOT NULL
            AND pp.no_telp <> ''
            AND pp.pendidikan_terakhir IS NOT NULL
            AND pp.pendidikan_terakhir <> ''
            AND pp.ktp_file_path IS NOT NULL
            AND pp.ktp_file_path <> ''
            AND pp.kk_file_path IS NOT NULL
            AND pp.kk_file_path <> ''
        )";
    }

    private function mapPegawaiListRow(object $row): object
    {
        $pegawai = (object) [
            'id' => (int) $row->id,
            'user_id' => $row->user_id !== null ? (int) $row->user_id : null,
            'nik' => $row->nik,
            'nip' => $row->nip ?? null,
            'nama' => $row->nama,
            'jenis_pegawai_id' => $row->jenis_pegawai_id !== null ? (int) $row->jenis_pegawai_id : null,
            'profesi_id' => $row->profesi_id !== null ? (int) $row->profesi_id : null,
            'jabatan_id' => $row->jabatan_id !== null ? (int) $row->jabatan_id : null,
            'status_pegawai' => $row->status_pegawai ?? null,
            'tgl_masuk' => $this->dateOrNull($row->tgl_masuk ?? null),
        ];

        $pegawai->user = (object) [
            'role' => $row->user_role ?? null,
        ];
        $pegawai->pribadi = $this->mapPribadiFromRow($row);
        $pegawai->jenisPegawai = (object) ['nama' => $row->jenis_pegawai_nama ?? null];
        $pegawai->profesi = (object) ['nama' => $row->profesi_nama ?? null];
        $pegawai->jabatan = (object) [
            'id' => isset($row->jabatan_id_detail) ? (int) $row->jabatan_id_detail : ($pegawai->jabatan_id ?? null),
            'nama' => $row->jabatan_nama ?? null,
            'unitKerja' => (object) [
                'id' => isset($row->unit_kerja_id) ? (int) $row->unit_kerja_id : null,
                'nama' => $row->unit_kerja_nama ?? null,
            ],
        ];

        return $pegawai;
    }

    private function mapPegawaiDetailRow(object $row): object
    {
        $pegawai = $this->mapPegawaiListRow($row);

        foreach (['tmt_cpns', 'tmt_pns', 'tmt_pangkat_akhir', 'masa_kerja'] as $field) {
            $pegawai->{$field} = $this->dateOrNull($row->{$field} ?? null);
        }

        $pegawai->user->username = $row->user_username ?? null;
        $pegawai->golonganRuang = (object) ['nama' => $row->golongan_ruang_nama ?? null];
        $pegawai->pangkat = (object) ['nama' => $row->pangkat_nama ?? null];

        return $pegawai;
    }

    private function mapPribadiFromRow(object $row): ?object
    {
        if (($row->pribadi_id ?? null) === null) {
            return null;
        }

        return (object) [
            'id' => (int) $row->pribadi_id,
            'pendidikan_terakhir' => $row->pribadi_pendidikan_terakhir ?? null,
            'no_kk' => $row->pribadi_no_kk ?? null,
            'tanggal_lahir' => $this->dateOrNull($row->pribadi_tanggal_lahir ?? null),
            'jenis_kelamin' => $row->pribadi_jenis_kelamin ?? null,
            'agama' => $row->pribadi_agama ?? null,
            'status_perkawinan' => $row->pribadi_status_perkawinan ?? null,
            'alamat' => $row->pribadi_alamat ?? null,
            'no_telp' => $row->pribadi_no_telp ?? null,
            'no_hp' => null,
            'email' => $row->pribadi_email ?? null,
            'foto_path' => $row->pribadi_foto_path ?? null,
            'ktp_file_path' => $row->pribadi_ktp_file_path ?? null,
            'kk_file_path' => $row->pribadi_kk_file_path ?? null,
        ];
    }

    private function getRiwayatJabatanByPegawaiId(int $pegawaiId): Collection
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
            ORDER BY jp.id DESC
        ', [$pegawaiId]))->map(function ($row) {
            $row->started_at = $this->dateOrNull($row->started_at ?? null);
            $row->ended_at = $this->dateOrNull($row->ended_at ?? null);
            $row->is_current = $this->isCurrentPeriod($row->started_at, $row->ended_at);
            $row->jabatan = (object) [
                'nama' => $row->jabatan_nama ?? null,
                'unitKerja' => (object) ['nama' => $row->unit_kerja_nama ?? null],
            ];

            return $row;
        });
    }

    private function getRiwayatPangkatByPegawaiId(int $pegawaiId): Collection
    {
        return collect(DB::select('
            SELECT
                pp.*,
                p.nama AS pangkat_nama,
                p.pejabat_penetap
            FROM pangkat_pegawai pp
            LEFT JOIN pangkat p ON p.id = pp.pangkat_id AND p.deleted_at IS NULL
            WHERE pp.pegawai_id = ?
                AND pp.deleted_at IS NULL
            ORDER BY pp.id DESC
        ', [$pegawaiId]))->map(function ($row) {
            $row->started_at = $this->dateOrNull($row->started_at ?? null);
            $row->ended_at = $this->dateOrNull($row->ended_at ?? null);
            $row->is_current = $this->isCurrentPeriod($row->started_at, $row->ended_at);
            $row->pangkat = (object) [
                'nama' => $row->pangkat_nama ?? null,
                'pejabat_penetap' => $row->pejabat_penetap ?? null,
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
            ORDER BY s.id DESC
        ', [$pegawaiId]))->map(function ($row) {
            $row->tanggal_terbit = $this->dateOrNull($row->tanggal_terbit ?? null);
            $row->tanggal_kadaluarsa = $this->dateOrNull($row->tanggal_kadaluarsa ?? null);
            $row->jenisSip = (object) ['nama' => $row->jenis_sip_nama ?? null];

            return $row;
        });
    }

    private function getJadwalDiklatByPegawaiId(int $pegawaiId): Collection
    {
        return collect(DB::select('
            SELECT
                ljd.*,
                d.id AS diklat_detail_id,
                d.nama_kegiatan,
                d.penyelenggara,
                d.tanggal_mulai,
                d.tanggal_selesai,
                d.waktu,
                d.jp,
                d.total_biaya,
                d.jenis_pelaksanaan,
                d.catatan,
                kd.nama AS kategori_nama,
                jd.nama AS jenis_nama,
                jb.nama AS jenis_biaya_nama,
                cb.id AS created_by_id,
                cb.nama AS created_by_nama
            FROM list_jadwal_diklat ljd
            LEFT JOIN diklat d ON d.id = ljd.diklat_id AND d.deleted_at IS NULL
            LEFT JOIN kategori_diklat kd ON kd.id = d.kategori_diklat_id
            LEFT JOIN jenis_diklat jd ON jd.id = d.jenis_diklat_id
            LEFT JOIN jenis_biaya jb ON jb.id = d.jenis_biaya_id
            LEFT JOIN pegawai cb ON cb.id = d.created_by AND cb.deleted_at IS NULL
            WHERE ljd.pegawai_id = ?
                AND ljd.deleted_at IS NULL
            ORDER BY ljd.id DESC
        ', [$pegawaiId]))->map(function ($row) {
            $row->diklat = (object) [
                'id' => $row->diklat_detail_id !== null ? (int) $row->diklat_detail_id : null,
                'nama_kegiatan' => $row->nama_kegiatan ?? null,
                'penyelenggara' => $row->penyelenggara ?? null,
                'tanggal_mulai' => $this->dateOrNull($row->tanggal_mulai ?? null),
                'tanggal_selesai' => $this->dateOrNull($row->tanggal_selesai ?? null),
                'waktu' => $row->waktu ?? null,
                'jp' => $row->jp ?? null,
                'total_biaya' => $row->total_biaya ?? null,
                'jenis_pelaksanaan' => $row->jenis_pelaksanaan ?? null,
                'catatan' => $row->catatan ?? null,
                'kategoriDiklat' => (object) ['nama' => $row->kategori_nama ?? null],
                'jenisDiklat' => (object) ['nama' => $row->jenis_nama ?? null],
                'jenisBiaya' => (object) ['nama' => $row->jenis_biaya_nama ?? null],
                'createdByPegawai' => (object) [
                    'id' => $row->created_by_id !== null ? (int) $row->created_by_id : null,
                    'nama' => $row->created_by_nama ?? null,
                ],
            ];

            return $row;
        });
    }

    private function selectCollection(string $sql, array $bindings = [], array $dateFields = []): Collection
    {
        if (in_array(null, $bindings, true)) {
            return collect();
        }

        return collect(DB::select($sql, $bindings))->map(function ($row) use ($dateFields) {
            foreach ($dateFields as $field) {
                $row->{$field} = $this->dateOrNull($row->{$field} ?? null);
            }

            return $row;
        });
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    private function isCurrentPeriod(?Carbon $startedAt, ?Carbon $endedAt): bool
    {
        $today = Carbon::today();

        return ($startedAt === null || $startedAt->lessThanOrEqualTo($today))
            && ($endedAt === null || $endedAt->greaterThanOrEqualTo($today));
    }

    private function filledString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
