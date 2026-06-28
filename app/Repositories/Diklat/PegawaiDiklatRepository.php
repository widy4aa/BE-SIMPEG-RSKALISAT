<?php

namespace App\Repositories\Diklat;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PegawaiDiklatRepository
{
    public function findPegawaiByUserId(int $userId): ?object
    {
        $row = DB::selectOne('
            SELECT *
            FROM pegawai
            WHERE user_id = ?
                AND deleted_at IS NULL
            LIMIT 1
        ', [$userId]);

        return $row ? $this->castPegawaiDates($row) : null;
    }

    public function findDiklatById(int $diklatId): ?object
    {
        return $this->findDiklatEntityById($diklatId);
    }

    public function getRiwayatDiklatByPegawaiId(int $pegawaiId): Collection
    {
        return $this->getJadwalRows('ljd.pegawai_id = ?', [$pegawaiId]);
    }

    public function getAllJadwalDiklat(): Collection
    {
        return $this->getJadwalRows('1 = 1');
    }

    public function getAllMasterDiklat(): Collection
    {
        return $this->getMasterDiklatRows('1 = 1');
    }

    public function getPaginatedRiwayatDiklatByPegawaiId(int $pegawaiId, int $perPage = 7, array $filters = []): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $offset = ($page - 1) * $perPage;
        [$filterSql, $bindings] = $this->buildDiklatFilterSql($filters, 'd');
        $whereSql = "ljd.pegawai_id = ? AND {$filterSql}";
        $bindings = [$pegawaiId, ...$bindings];

        $totalRow = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM list_jadwal_diklat ljd
            INNER JOIN diklat d ON d.id = ljd.diklat_id AND d.deleted_at IS NULL
            LEFT JOIN kategori_diklat kd ON kd.id = d.kategori_diklat_id
            LEFT JOIN jenis_diklat jd ON jd.id = d.jenis_diklat_id
            WHERE ljd.deleted_at IS NULL
                AND {$whereSql}
        ", $bindings);

        $rows = $this->selectJadwalRows($whereSql, $bindings, $perPage, $offset);

        return $this->paginate(collect($rows)->map(fn ($row) => $this->mapJadwalRow($row)), (int) ($totalRow->total ?? 0), $perPage, $page);
    }

    public function getJadwalSummaryByPegawaiId(int $pegawaiId): array
    {
        $row = DB::selectOne('
            SELECT
                COUNT(*) AS total_riwayat,
                SUM(CASE WHEN status_diklat = ? THEN 1 ELSE 0 END) AS selesai,
                SUM(CASE WHEN status_diklat = ? THEN 1 ELSE 0 END) AS akan_datang
            FROM list_jadwal_diklat
            WHERE pegawai_id = ?
                AND deleted_at IS NULL
        ', ['sudah terlaksana', 'belum terlaksana', $pegawaiId]);

        return [
            'total_riwayat' => (int) ($row->total_riwayat ?? 0),
            'selesai' => (int) ($row->selesai ?? 0),
            'akan_datang' => (int) ($row->akan_datang ?? 0),
        ];
    }

    public function getPaginatedMasterDiklat(int $perPage = 7, array $filters = []): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();
        $offset = ($page - 1) * $perPage;
        $filters = $this->withDefaultJenisPelaksanaan($filters, 'internal');
        [$whereSql, $bindings] = $this->buildDiklatFilterSql($filters, 'd');

        $totalRow = DB::selectOne("
            SELECT COUNT(*) AS total
            FROM diklat d
            LEFT JOIN kategori_diklat kd ON kd.id = d.kategori_diklat_id
            LEFT JOIN jenis_diklat jd ON jd.id = d.jenis_diklat_id
            WHERE {$whereSql}
        ", $bindings);

        $rows = DB::select("
            {$this->masterDiklatSelectSql()}
            WHERE {$whereSql}
            ORDER BY d.id DESC
            LIMIT ? OFFSET ?
        ", [...$bindings, $perPage, $offset]);

        return $this->paginate(collect($rows)->map(fn ($row) => $this->mapDiklatRow($row)), (int) ($totalRow->total ?? 0), $perPage, $page);
    }

    public function getMasterDiklatStats(array $filters = []): array
    {
        $filters = $this->withDefaultJenisPelaksanaan($filters, 'internal');
        [$whereSql, $bindings] = $this->buildDiklatFilterSql($filters, 'd');
        $today = Carbon::today()->toDateString();

        $row = DB::selectOne("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN DATE(d.tanggal_selesai) < ? THEN 1 ELSE 0 END) AS selesai,
                SUM(CASE WHEN DATE(d.tanggal_mulai) <= ? AND DATE(d.tanggal_selesai) >= ? THEN 1 ELSE 0 END) AS berlangsung,
                SUM(CASE WHEN DATE(d.tanggal_mulai) > ? THEN 1 ELSE 0 END) AS mendatang
            FROM diklat d
            LEFT JOIN kategori_diklat kd ON kd.id = d.kategori_diklat_id
            LEFT JOIN jenis_diklat jd ON jd.id = d.jenis_diklat_id
            WHERE {$whereSql}
        ", [$today, $today, $today, $today, ...$bindings]);

        return [
            'total' => (int) ($row->total ?? 0),
            'selesai' => (int) ($row->selesai ?? 0),
            'berlangsung' => (int) ($row->berlangsung ?? 0),
            'mendatang' => (int) ($row->mendatang ?? 0),
        ];
    }

    public function firstOrCreateKategoriByNama(string $nama): object
    {
        return $this->firstOrCreateMasterRow('kategori_diklat', trim($nama));
    }

    public function firstOrCreateJenisByNama(string $nama): object
    {
        return $this->firstOrCreateMasterRow('jenis_diklat', trim($nama));
    }

    public function firstOrCreateJenisBiayaByNama(string $nama): object
    {
        return $this->firstOrCreateMasterRow('jenis_biaya', trim($nama));
    }

    public function createDiklat(array $attributes): object
    {
        $now = Carbon::now()->toDateTimeString();
        DB::insert('
            INSERT INTO diklat (
                jenis_diklat_id, kategori_diklat_id, created_by, nama_kegiatan, penyelenggara,
                tanggal_mulai, tanggal_selesai, tempat, waktu, jp, total_biaya,
                jenis_biaya_id, jenis_pelaksanaan, catatan, created_at, updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ', [
            $attributes['jenis_diklat_id'] ?? null,
            $attributes['kategori_diklat_id'] ?? null,
            $attributes['created_by'] ?? null,
            $attributes['nama_kegiatan'] ?? null,
            $attributes['penyelenggara'] ?? null,
            $attributes['tanggal_mulai'] ?? null,
            $attributes['tanggal_selesai'] ?? null,
            $attributes['tempat'] ?? null,
            $attributes['waktu'] ?? null,
            $attributes['jp'] ?? null,
            $attributes['total_biaya'] ?? null,
            $attributes['jenis_biaya_id'] ?? null,
            $attributes['jenis_pelaksanaan'] ?? null,
            $attributes['catatan'] ?? null,
            $now,
            $now,
        ]);

        return $this->findDiklatEntityById((int) DB::getPdo()->lastInsertId());
    }

    public function createJadwalDiklat(array $attributes): object
    {
        $now = Carbon::now()->toDateTimeString();
        DB::insert('
            INSERT INTO list_jadwal_diklat (
                diklat_id, pegawai_id, sertif_file_path, no_sertif, uploaded_at,
                status_diklat, status_kelayakan, status_validasi, created_at, updated_at
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ', [
            $attributes['diklat_id'] ?? null,
            $attributes['pegawai_id'] ?? null,
            $attributes['sertif_file_path'] ?? null,
            $attributes['no_sertif'] ?? null,
            $attributes['uploaded_at'] ?? null,
            $attributes['status_diklat'] ?? null,
            $attributes['status_kelayakan'] ?? null,
            $attributes['status_validasi'] ?? null,
            $now,
            $now,
        ]);

        return $this->findJadwalEntityById((int) DB::getPdo()->lastInsertId());
    }

    public function findJadwalByDiklatIdAndPegawaiId(int $diklatId, int $pegawaiId): ?object
    {
        $row = DB::selectOne('
            SELECT id
            FROM list_jadwal_diklat
            WHERE diklat_id = ?
                AND pegawai_id = ?
                AND deleted_at IS NULL
            LIMIT 1
        ', [$diklatId, $pegawaiId]);

        return $row ? $this->findJadwalEntityById((int) $row->id) : null;
    }

    public function findJadwalById(int $jadwalId): ?object
    {
        return $this->findJadwalEntityById($jadwalId);
    }

    public function saveDiklat(object $diklat): bool
    {
        return DB::update('
            UPDATE diklat
            SET
                jenis_diklat_id = ?,
                kategori_diklat_id = ?,
                created_by = ?,
                nama_kegiatan = ?,
                penyelenggara = ?,
                tanggal_mulai = ?,
                tanggal_selesai = ?,
                tempat = ?,
                waktu = ?,
                jp = ?,
                total_biaya = ?,
                jenis_biaya_id = ?,
                jenis_pelaksanaan = ?,
                catatan = ?,
                updated_at = ?
            WHERE id = ?
                AND deleted_at IS NULL
        ', [
            $diklat->jenis_diklat_id ?? null,
            $diklat->kategori_diklat_id ?? null,
            $diklat->created_by ?? null,
            $diklat->nama_kegiatan ?? null,
            $diklat->penyelenggara ?? null,
            $this->dateValue($diklat->tanggal_mulai ?? null),
            $this->dateValue($diklat->tanggal_selesai ?? null),
            $diklat->tempat ?? null,
            $this->timeValue($diklat->waktu ?? null),
            $diklat->jp ?? null,
            $diklat->total_biaya ?? null,
            $diklat->jenis_biaya_id ?? null,
            $diklat->jenis_pelaksanaan ?? null,
            $diklat->catatan ?? null,
            Carbon::now()->toDateTimeString(),
            $diklat->id,
        ]) > 0;
    }

    public function saveJadwalDiklat(object $jadwal): bool
    {
        return DB::update('
            UPDATE list_jadwal_diklat
            SET
                diklat_id = ?,
                pegawai_id = ?,
                sertif_file_path = ?,
                no_sertif = ?,
                uploaded_at = ?,
                status_diklat = ?,
                status_kelayakan = ?,
                status_validasi = ?,
                updated_at = ?
            WHERE id = ?
                AND deleted_at IS NULL
        ', [
            $jadwal->diklat_id ?? null,
            $jadwal->pegawai_id ?? null,
            $jadwal->sertif_file_path ?? null,
            $jadwal->no_sertif ?? null,
            $this->dateTimeValue($jadwal->uploaded_at ?? null),
            $jadwal->status_diklat ?? null,
            $jadwal->status_kelayakan ?? null,
            $jadwal->status_validasi ?? null,
            Carbon::now()->toDateTimeString(),
            $jadwal->id,
        ]) > 0;
    }

    public function deleteJadwalDiklat(object $jadwal): bool
    {
        $now = Carbon::now()->toDateTimeString();

        return DB::update('
            UPDATE list_jadwal_diklat
            SET deleted_at = ?, updated_at = ?
            WHERE id = ?
                AND deleted_at IS NULL
        ', [$now, $now, $jadwal->id]) > 0;
    }

    public function countRemainingJadwalByDiklatId(int $diklatId): int
    {
        $row = DB::selectOne('
            SELECT COUNT(*) AS total
            FROM list_jadwal_diklat
            WHERE diklat_id = ?
                AND deleted_at IS NULL
        ', [$diklatId]);

        return (int) ($row->total ?? 0);
    }

    public function deleteDiklat(object $diklat): bool
    {
        $now = Carbon::now()->toDateTimeString();

        return DB::update('
            UPDATE diklat
            SET deleted_at = ?, updated_at = ?
            WHERE id = ?
                AND deleted_at IS NULL
        ', [$now, $now, $diklat->id]) > 0;
    }

    public function getAllPegawaiWithUnitKerjaProfesi(): Collection
    {
        return collect(DB::select('
            SELECT
                p.id,
                p.nik,
                p.nama,
                j.nama AS jabatan_nama,
                uk.nama AS unit_kerja_nama,
                pr.nama AS profesi_nama
            FROM pegawai p
            LEFT JOIN jabatan j ON j.id = p.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            LEFT JOIN profesi pr ON pr.id = p.profesi_id
            WHERE p.deleted_at IS NULL
            ORDER BY p.nama ASC
        '))->map(function ($row) {
            $row->jabatan = (object) [
                'nama' => $row->jabatan_nama ?? null,
                'unitKerja' => (object) ['nama' => $row->unit_kerja_nama ?? null],
            ];
            $row->profesi = (object) ['nama' => $row->profesi_nama ?? null];

            return $row;
        });
    }

    public function getPesertaDiklatIds(int $diklatId): array
    {
        return array_map(
            fn ($row) => (int) $row->pegawai_id,
            DB::select('
                SELECT pegawai_id
                FROM list_jadwal_diklat
                WHERE diklat_id = ?
                    AND deleted_at IS NULL
            ', [$diklatId])
        );
    }

    public function deleteJadwalNotInPegawaiIds(int $diklatId, array $pegawaiIds): void
    {
        $now = Carbon::now()->toDateTimeString();

        if ($pegawaiIds === []) {
            DB::update('
                UPDATE list_jadwal_diklat
                SET deleted_at = ?, updated_at = ?
                WHERE diklat_id = ?
                    AND deleted_at IS NULL
            ', [$now, $now, $diklatId]);

            return;
        }

        $placeholders = implode(',', array_fill(0, count($pegawaiIds), '?'));
        DB::update("
            UPDATE list_jadwal_diklat
            SET deleted_at = ?, updated_at = ?
            WHERE diklat_id = ?
                AND deleted_at IS NULL
                AND pegawai_id NOT IN ({$placeholders})
        ", [$now, $now, $diklatId, ...$pegawaiIds]);
    }

    public function getJadwalDiklatMenungguKelayakan(): Collection
    {
        return $this->getJadwalRows('ljd.status_kelayakan IS NULL AND ljd.sertif_file_path IS NOT NULL AND d.jenis_pelaksanaan = ?', ['external']);
    }

    public function getJadwalDiklatMenungguValidasi(): Collection
    {
        return $this->getJadwalRows('ljd.sertif_file_path IS NOT NULL AND ljd.status_validasi IS NULL AND d.jenis_pelaksanaan = ?', ['internal']);
    }

    public function getRekapLaporanDiklatInternal(Carbon $startDate, Carbon $endDate): Collection
    {
        $diklatRows = DB::select('
            SELECT
                d.*,
                jd.nama AS jenis_diklat_nama,
                jb.nama AS jenis_biaya_nama,
                (
                    SELECT COUNT(*)
                    FROM list_jadwal_diklat ljd_all
                    WHERE ljd_all.diklat_id = d.id
                        AND ljd_all.deleted_at IS NULL
                ) AS total_peserta,
                (
                    SELECT COUNT(*)
                    FROM list_jadwal_diklat ljd_valid
                    WHERE ljd_valid.diklat_id = d.id
                        AND ljd_valid.deleted_at IS NULL
                        AND ljd_valid.status_validasi = ?
                        AND ljd_valid.sertif_file_path IS NOT NULL
                ) AS total_peserta_validasi
            FROM diklat d
            LEFT JOIN jenis_diklat jd ON jd.id = d.jenis_diklat_id
            LEFT JOIN jenis_biaya jb ON jb.id = d.jenis_biaya_id
            WHERE d.jenis_pelaksanaan = ?
                AND d.created_at BETWEEN ? AND ?
                AND d.deleted_at IS NULL
            ORDER BY d.id DESC
        ', ['valid', 'internal', $startDate->toDateTimeString(), $endDate->toDateTimeString()]);

        $diklatList = collect($diklatRows)->map(fn ($row) => $this->mapDiklatRow($row));
        $diklatIds = $diklatList->pluck('id')->all();

        if ($diklatIds === []) {
            return $diklatList;
        }

        $placeholders = implode(',', array_fill(0, count($diklatIds), '?'));
        $jadwalRows = DB::select("
            SELECT
                ljd.*,
                p.nama AS pegawai_nama,
                p.nik AS pegawai_nik,
                p.nip AS pegawai_nip,
                j.nama AS jabatan_nama,
                uk.nama AS unit_kerja_nama
            FROM list_jadwal_diklat ljd
            INNER JOIN pegawai p ON p.id = ljd.pegawai_id AND p.deleted_at IS NULL
            LEFT JOIN jabatan j ON j.id = p.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            WHERE ljd.diklat_id IN ({$placeholders})
                AND ljd.deleted_at IS NULL
                AND ljd.status_validasi = ?
                AND ljd.sertif_file_path IS NOT NULL
            ORDER BY ljd.id DESC
        ", [...$diklatIds, 'valid']);

        $jadwalByDiklat = collect($jadwalRows)
            ->map(fn ($row) => $this->mapLaporanJadwalRow($row))
            ->groupBy('diklat_id');

        return $diklatList->map(function ($diklat) use ($jadwalByDiklat) {
            $diklat->jadwalPeserta = $jadwalByDiklat->get($diklat->id, collect())->values();

            return $diklat;
        });
    }

    private function getJadwalRows(string $whereSql, array $bindings = []): Collection
    {
        return collect($this->selectJadwalRows($whereSql, $bindings))
            ->map(fn ($row) => $this->mapJadwalRow($row));
    }

    private function findDiklatEntityById(int $diklatId): ?object
    {
        $row = DB::selectOne("
            {$this->masterDiklatSelectSql()}
            WHERE d.id = ?
                AND d.deleted_at IS NULL
            LIMIT 1
        ", [$diklatId]);

        return $row ? $this->mapDiklatRow($row) : null;
    }

    private function findJadwalEntityById(int $jadwalId): ?object
    {
        $rows = $this->selectJadwalRows('ljd.id = ?', [$jadwalId], 1, 0);

        return isset($rows[0]) ? $this->mapJadwalRow($rows[0]) : null;
    }

    private function firstOrCreateMasterRow(string $table, string $nama): object
    {
        $row = DB::selectOne("
            SELECT id, nama
            FROM {$table}
            WHERE nama = ?
            LIMIT 1
        ", [$nama]);

        if ($row !== null) {
            return $row;
        }

        $now = Carbon::now()->toDateTimeString();
        DB::insert("
            INSERT INTO {$table} (nama, created_at, updated_at)
            VALUES (?, ?, ?)
        ", [$nama, $now, $now]);

        return (object) [
            'id' => (int) DB::getPdo()->lastInsertId(),
            'nama' => $nama,
        ];
    }

    private function selectJadwalRows(string $whereSql, array $bindings = [], ?int $limit = null, ?int $offset = null): array
    {
        $paginationSql = $limit === null ? '' : ' LIMIT ? OFFSET ?';
        $paginationBindings = $limit === null ? [] : [$limit, $offset ?? 0];

        return DB::select("
            SELECT
                ljd.*,
                d.id AS diklat_detail_id,
                d.created_by,
                d.jenis_diklat_id,
                d.kategori_diklat_id,
                d.jenis_biaya_id,
                d.jenis_pelaksanaan,
                d.nama_kegiatan,
                d.penyelenggara,
                d.tanggal_mulai,
                d.tanggal_selesai,
                d.tempat,
                d.waktu,
                d.jp,
                d.total_biaya,
                d.catatan,
                kd.nama AS kategori_diklat_nama,
                jd.nama AS jenis_diklat_nama,
                jb.nama AS jenis_biaya_nama,
                cb.nama AS created_by_nama,
                p.nama AS pegawai_nama,
                p.nik AS pegawai_nik
            FROM list_jadwal_diklat ljd
            LEFT JOIN diklat d ON d.id = ljd.diklat_id AND d.deleted_at IS NULL
            LEFT JOIN kategori_diklat kd ON kd.id = d.kategori_diklat_id
            LEFT JOIN jenis_diklat jd ON jd.id = d.jenis_diklat_id
            LEFT JOIN jenis_biaya jb ON jb.id = d.jenis_biaya_id
            LEFT JOIN pegawai cb ON cb.id = d.created_by AND cb.deleted_at IS NULL
            LEFT JOIN pegawai p ON p.id = ljd.pegawai_id AND p.deleted_at IS NULL
            WHERE ljd.deleted_at IS NULL
                AND {$whereSql}
            ORDER BY ljd.id DESC
            {$paginationSql}
        ", [...$bindings, ...$paginationBindings]);
    }

    private function getMasterDiklatRows(string $whereSql, array $bindings = []): Collection
    {
        return collect(DB::select("
            {$this->masterDiklatSelectSql()}
            WHERE {$whereSql}
            ORDER BY d.id DESC
        ", $bindings))->map(fn ($row) => $this->mapDiklatRow($row));
    }

    private function masterDiklatSelectSql(): string
    {
        return '
            SELECT
                d.*,
                kd.nama AS kategori_diklat_nama,
                jd.nama AS jenis_diklat_nama,
                jb.nama AS jenis_biaya_nama,
                cb.nama AS created_by_nama,
                (
                    SELECT COUNT(*)
                    FROM list_jadwal_diklat ljd
                    WHERE ljd.diklat_id = d.id
                        AND ljd.deleted_at IS NULL
                ) AS jadwal_peserta_count,
                (
                    SELECT COUNT(*)
                    FROM list_jadwal_diklat ljd_validasi
                    WHERE ljd_validasi.diklat_id = d.id
                        AND ljd_validasi.deleted_at IS NULL
                        AND ljd_validasi.status_validasi IS NOT NULL
                        AND ljd_validasi.status_validasi <> \'\'
                ) AS peserta_sudah_validasi_count,
                (
                    SELECT COUNT(*)
                    FROM list_jadwal_diklat ljd_belum_validasi
                    WHERE ljd_belum_validasi.diklat_id = d.id
                        AND ljd_belum_validasi.deleted_at IS NULL
                        AND (
                            ljd_belum_validasi.status_validasi IS NULL
                            OR ljd_belum_validasi.status_validasi = \'\'
                        )
                ) AS peserta_belum_validasi_count
            FROM diklat d
            LEFT JOIN kategori_diklat kd ON kd.id = d.kategori_diklat_id
            LEFT JOIN jenis_diklat jd ON jd.id = d.jenis_diklat_id
            LEFT JOIN jenis_biaya jb ON jb.id = d.jenis_biaya_id
            LEFT JOIN pegawai cb ON cb.id = d.created_by AND cb.deleted_at IS NULL
        ';
    }

    private function buildDiklatFilterSql(array $filters, string $alias = 'd'): array
    {
        $where = ["{$alias}.deleted_at IS NULL"];
        $bindings = [];

        $search = $this->filledString($filters['search'] ?? null);
        if ($search !== null) {
            $like = "%{$search}%";
            $where[] = "(
                {$alias}.nama_kegiatan LIKE ?
                OR {$alias}.penyelenggara LIKE ?
                OR kd.nama LIKE ?
                OR jd.nama LIKE ?
            )";
            array_push($bindings, $like, $like, $like, $like);
        }

        $jenis = $this->filledString($filters['jenis'] ?? null);
        if ($jenis !== null) {
            $where[] = 'jd.nama LIKE ?';
            $bindings[] = "%{$jenis}%";
        }

        $jenisPelaksanaan = $this->filledString(
            $filters['jenis_pelaksana'] ?? $filters['jenis_pelaksanaan'] ?? null
        );
        if ($jenisPelaksanaan !== null) {
            $where[] = "LOWER({$alias}.jenis_pelaksanaan) = ?";
            $bindings[] = strtolower($jenisPelaksanaan);
        }

        $status = $this->filledString($filters['status'] ?? null);
        if ($status !== null) {
            $today = Carbon::today()->toDateString();

            if ($status === 'mendatang') {
                $where[] = "DATE({$alias}.tanggal_mulai) > ?";
                $bindings[] = $today;
            } elseif ($status === 'selesai') {
                $where[] = "DATE({$alias}.tanggal_selesai) < ?";
                $bindings[] = $today;
            } elseif ($status === 'berlangsung') {
                $where[] = "DATE({$alias}.tanggal_mulai) <= ? AND DATE({$alias}.tanggal_selesai) >= ?";
                array_push($bindings, $today, $today);
            }
        }

        return [implode(' AND ', $where), $bindings];
    }

    private function withDefaultJenisPelaksanaan(array $filters, string $default): array
    {
        if (
            ! array_key_exists('jenis_pelaksana', $filters)
            && ! array_key_exists('jenis_pelaksanaan', $filters)
        ) {
            $filters['jenis_pelaksana'] = $default;
        }

        return $filters;
    }

    private function mapJadwalRow(object $row): object
    {
        $row->uploaded_at = $this->dateTimeOrNull($row->uploaded_at ?? null);
        $row->diklat = (object) [
            'id' => $row->diklat_detail_id !== null ? (int) $row->diklat_detail_id : null,
            'created_by' => $row->created_by ?? null,
            'jenis_diklat_id' => $row->jenis_diklat_id ?? null,
            'kategori_diklat_id' => $row->kategori_diklat_id ?? null,
            'jenis_biaya_id' => $row->jenis_biaya_id ?? null,
            'jenis_pelaksanaan' => $row->jenis_pelaksanaan ?? null,
            'nama_kegiatan' => $row->nama_kegiatan ?? null,
            'penyelenggara' => $row->penyelenggara ?? null,
            'tanggal_mulai' => $this->dateOrNull($row->tanggal_mulai ?? null),
            'tanggal_selesai' => $this->dateOrNull($row->tanggal_selesai ?? null),
            'tempat' => $row->tempat ?? null,
            'waktu' => $this->dateTimeOrNull($row->waktu ?? null),
            'jp' => $row->jp ?? null,
            'total_biaya' => $row->total_biaya ?? null,
            'catatan' => $row->catatan ?? null,
            'kategoriDiklat' => (object) ['nama' => $row->kategori_diklat_nama ?? null],
            'jenisDiklat' => (object) ['nama' => $row->jenis_diklat_nama ?? null],
            'jenisBiaya' => (object) ['nama' => $row->jenis_biaya_nama ?? null],
            'createdByPegawai' => (object) ['nama' => $row->created_by_nama ?? null],
        ];
        $row->pegawai = (object) [
            'id' => $row->pegawai_id !== null ? (int) $row->pegawai_id : null,
            'nama' => $row->pegawai_nama ?? null,
            'nik' => $row->pegawai_nik ?? null,
        ];

        return $row;
    }

    private function mapDiklatRow(object $row): object
    {
        $row->tanggal_mulai = $this->dateOrNull($row->tanggal_mulai ?? null);
        $row->tanggal_selesai = $this->dateOrNull($row->tanggal_selesai ?? null);
        $row->waktu = $this->dateTimeOrNull($row->waktu ?? null);
        $row->kategoriDiklat = (object) ['nama' => $row->kategori_diklat_nama ?? null];
        $row->jenisDiklat = (object) ['nama' => $row->jenis_diklat_nama ?? null];
        $row->jenisBiaya = (object) ['nama' => $row->jenis_biaya_nama ?? null];
        $row->createdByPegawai = (object) ['nama' => $row->created_by_nama ?? null];
        $row->jadwal_peserta_count = (int) ($row->jadwal_peserta_count ?? 0);
        $row->peserta_sudah_validasi_count = (int) ($row->peserta_sudah_validasi_count ?? 0);
        $row->peserta_belum_validasi_count = (int) ($row->peserta_belum_validasi_count ?? 0);
        $row->total_peserta = (int) ($row->total_peserta ?? 0);
        $row->total_peserta_validasi = (int) ($row->total_peserta_validasi ?? 0);
        $row->jadwalPeserta = collect();

        return $row;
    }

    private function mapLaporanJadwalRow(object $row): object
    {
        $row->pegawai = (object) [
            'nama' => $row->pegawai_nama ?? null,
            'nik' => $row->pegawai_nik ?? null,
            'nip' => $row->pegawai_nip ?? null,
            'jabatan' => (object) [
                'nama' => $row->jabatan_nama ?? null,
                'unitKerja' => (object) ['nama' => $row->unit_kerja_nama ?? null],
            ],
        ];

        return $row;
    }

    private function castPegawaiDates(object $row): object
    {
        foreach (['tgl_masuk', 'tmt_cpns', 'tmt_pns', 'tmt_pangkat_akhir', 'masa_kerja'] as $field) {
            $row->{$field} = $this->dateOrNull($row->{$field} ?? null);
        }

        return $row;
    }

    private function paginate(Collection $items, int $total, int $perPage, int $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value)->startOfDay() : null;
    }

    private function dateTimeOrNull(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }

    private function dateValue(mixed $value): ?string
    {
        return $value instanceof Carbon ? $value->toDateString() : ($value ?: null);
    }

    private function dateTimeValue(mixed $value): ?string
    {
        return $value instanceof Carbon ? $value->toDateTimeString() : ($value ?: null);
    }

    private function timeValue(mixed $value): ?string
    {
        return $value instanceof Carbon ? $value->format('H:i:s') : ($value ?: null);
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
