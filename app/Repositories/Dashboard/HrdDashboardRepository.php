<?php

namespace App\Repositories\Dashboard;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HrdDashboardRepository
{
    public function getDashboardStats(?string $type = null): array
    {
        $result = [];
        $totalPegawai = $this->countRaw('
            SELECT COUNT(*) AS total
            FROM pegawai
            WHERE deleted_at IS NULL
        ');

        if ($type === null || $type === 'pegawai') {
            // Pegawai kurang lengkap: 
            // Either pribadi doesn't exist, or has null in important fields, or pegawai has null in important fields.
            $totalKurangLengkap = $this->countRaw('
                SELECT COUNT(*) AS total
                FROM pegawai p
                LEFT JOIN pegawai_pribadi pp
                    ON pp.pegawai_id = p.id
                    AND pp.deleted_at IS NULL
                WHERE p.deleted_at IS NULL
                    AND (
                        ((p.nik IS NULL OR p.nik = '') AND (p.nip IS NULL OR p.nip = ''))
                        OR p.jenis_pegawai_id IS NULL
                        OR p.profesi_id IS NULL
                        OR p.tgl_masuk IS NULL
                        OR pp.id IS NULL
                        OR pp.tanggal_lahir IS NULL
                        OR pp.jenis_kelamin IS NULL
                        OR pp.agama IS NULL
                        OR pp.alamat IS NULL
                        OR pp.no_telp IS NULL
                        OR pp.pendidikan_terakhir IS NULL
                        OR pp.ktp_file_path IS NULL
                        OR pp.ktp_file_path = ''
                        OR pp.kk_file_path IS NULL
                        OR pp.kk_file_path = ''
                    )
            ');

            $totalLengkap = max(0, $totalPegawai - $totalKurangLengkap);

            // Group by jenis_pegawai
            $jenisPegawaiData = $this->pluckRaw('
                SELECT jp.nama AS label, COUNT(*) AS value
                FROM pegawai p
                INNER JOIN jenis_pegawai jp ON p.jenis_pegawai_id = jp.id
                WHERE p.deleted_at IS NULL
                GROUP BY jp.id, jp.nama
            ');

            // Group by profesi
            $profesiData = $this->pluckRaw('
                SELECT pr.nama AS label, COUNT(*) AS value
                FROM pegawai p
                INNER JOIN profesi pr ON p.profesi_id = pr.id
                WHERE p.deleted_at IS NULL
                GROUP BY pr.id, pr.nama
            ');

            // Group by tingkat pendidikan terakhir
            $pendidikanData = $this->pluckRaw('
                SELECT pendidikan_terakhir AS label, COUNT(*) AS value
                FROM pegawai_pribadi
                WHERE pendidikan_terakhir IS NOT NULL
                    AND deleted_at IS NULL
                GROUP BY pendidikan_terakhir
            ');

            // Tahun masuk 5 tahun terakhir
            $currentYear = (int) date('Y');
            $fiveYearsAgo = $currentYear - 4; // 5 years including current year (e.g. 2022-2026)

            $tahunMasukCounts = [];
            $tahunMasukRows = DB::select('
                SELECT YEAR(tgl_masuk) AS year, COUNT(*) AS count
                FROM pegawai
                WHERE tgl_masuk IS NOT NULL
                    AND YEAR(tgl_masuk) >= ?
                    AND deleted_at IS NULL
                GROUP BY YEAR(tgl_masuk)
            ', [$fiveYearsAgo]);

            foreach ($tahunMasukRows as $row) {
                $tahunMasukCounts[(int) $row->year] = (int) $row->count;
            }

            // Ensure all 5 years are present in the array even if 0
            $tahunMasuk = [];
            for ($i = $fiveYearsAgo; $i <= $currentYear; $i++) {
                $tahunMasuk[(string)$i] = $tahunMasukCounts[$i] ?? 0;
            }

            $result['pegawai'] = [
                'total_pegawai' => $totalPegawai,
                'total_pegawai_kurang_lengkap' => $totalKurangLengkap,
                'total_pegawai_lengkap' => $totalLengkap,
                'jenis_pegawai' => $jenisPegawaiData,
                'profesi' => $profesiData,
                'tingkat_pendidikan' => $pendidikanData,
                'tahun_masuk_5_tahun_terakhir' => $tahunMasuk,
            ];
        }

        if ($type === null || $type === 'diklat_asn' || $type === 'diklat_tenkes') {
            // Fetch categories to map names easily
            $kategoriDiklatMap = [];
            $kategoriDiklatRows = DB::select('
                SELECT id, nama
                FROM kategori_diklat
            ');

            foreach ($kategoriDiklatRows as $row) {
                $kategoriDiklatMap[(int) $row->id] = $row->nama;
            }

            // Helper function to calculate stats for a specific jenis diklat
            $getDiklatStats = function ($jenisDiklatId) use ($totalPegawai, $kategoriDiklatMap) {
                $now = Carbon::now()->toDateTimeString();

                $totalDiklat = $this->countRaw('
                    SELECT COUNT(*) AS total
                    FROM diklat
                    WHERE jenis_diklat_id = ?
                        AND deleted_at IS NULL
                ', [$jenisDiklatId]);
                
                $selesai = $this->countRaw('
                    SELECT COUNT(*) AS total
                    FROM diklat
                    WHERE jenis_diklat_id = ?
                        AND tanggal_selesai < ?
                        AND deleted_at IS NULL
                ', [$jenisDiklatId, $now]);

                $berlangsung = $this->countRaw('
                    SELECT COUNT(*) AS total
                    FROM diklat
                    WHERE jenis_diklat_id = ?
                        AND tanggal_mulai <= ?
                        AND tanggal_selesai >= ?
                        AND deleted_at IS NULL
                ', [$jenisDiklatId, $now, $now]);

                // Total pegawai yang SUDAH mengikuti (status = sudah terlaksana)
                $pegawaiSudahIkut = $this->countRaw('
                    SELECT COUNT(DISTINCT ljd.pegawai_id) AS total
                    FROM list_jadwal_diklat ljd
                    INNER JOIN diklat d ON ljd.diklat_id = d.id
                    WHERE d.jenis_diklat_id = ?
                        AND ljd.status_diklat = ?
                        AND ljd.deleted_at IS NULL
                        AND d.deleted_at IS NULL
                ', [$jenisDiklatId, 'sudah terlaksana']);

                // Pegawai yang BELUM mengikuti
                // Asumsi: Semua pegawai dikurangi yang pernah ikut/sedang ikut/belum terlaksana
                // Atau cukup total pegawai - yang sudah terdaftar di jenis ini
                $pegawaiTerdaftar = $this->countRaw('
                    SELECT COUNT(DISTINCT ljd.pegawai_id) AS total
                    FROM list_jadwal_diklat ljd
                    INNER JOIN diklat d ON ljd.diklat_id = d.id
                    WHERE d.jenis_diklat_id = ?
                        AND ljd.deleted_at IS NULL
                        AND d.deleted_at IS NULL
                ', [$jenisDiklatId]);

                $pegawaiBelumIkut = max(0, $totalPegawai - $pegawaiTerdaftar);

                // Diklat per kategori
                $diklatPerKategori = [];
                foreach ($kategoriDiklatMap as $kategoriNama) {
                    $diklatPerKategori[$kategoriNama] = 0;
                }

                $kategoriCounts = DB::select('
                    SELECT kategori_diklat_id, COUNT(*) AS count
                    FROM diklat
                    WHERE jenis_diklat_id = ?
                        AND deleted_at IS NULL
                    GROUP BY kategori_diklat_id
                ', [$jenisDiklatId]);

                foreach ($kategoriCounts as $kc) {
                    if (isset($kategoriDiklatMap[$kc->kategori_diklat_id])) {
                        $diklatPerKategori[$kategoriDiklatMap[$kc->kategori_diklat_id]] = $kc->count;
                    }
                }

                return [
                    'total_diklat' => $totalDiklat,
                    'selesai' => $selesai,
                    'berlangsung' => $berlangsung,
                    'pegawai_sudah_ikut' => $pegawaiSudahIkut,
                    'pegawai_belum_ikut' => $pegawaiBelumIkut,
                    'diklat_per_kategori' => $diklatPerKategori,
                ];
            };

            if ($type === null || $type === 'diklat_asn') {
                $result['diklat_asn'] = $getDiklatStats(1);
            }

            if ($type === null || $type === 'diklat_tenkes') {
                $result['diklat_tenkes'] = $getDiklatStats(2);
            }
        }

        return $result;
    }

    private function countRaw(string $sql, array $bindings = []): int
    {
        $row = DB::selectOne($sql, $bindings);

        return (int) ($row->total ?? 0);
    }

    private function pluckRaw(string $sql, array $bindings = []): array
    {
        $result = [];

        foreach (DB::select($sql, $bindings) as $row) {
            $result[$row->label] = (int) $row->value;
        }

        return $result;
    }
}
