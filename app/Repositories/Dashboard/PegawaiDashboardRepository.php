<?php

namespace App\Repositories\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PegawaiDashboardRepository
{
    public function findPegawaiDashboardByUserId(int $userId): ?object
    {
        $row = DB::selectOne('
            SELECT
                p.id,
                p.nama,
                p.nip,
                j.nama AS jabatan_nama,
                jp.nama AS jenis_pegawai_nama,
                uk.nama AS unit_kerja_nama,
                (
                    SELECT COUNT(*)
                    FROM list_jadwal_diklat ljd
                    WHERE ljd.pegawai_id = p.id
                        AND ljd.status_diklat = ?
                        AND ljd.deleted_at IS NULL
                ) AS jumlah_diklat_selesai,
                (
                    SELECT COUNT(*)
                    FROM list_jadwal_diklat ljd
                    WHERE ljd.pegawai_id = p.id
                        AND ljd.status_diklat IN (?, ?)
                        AND ljd.deleted_at IS NULL
                ) AS jumlah_diklat_belum_selesai
            FROM pegawai p
            LEFT JOIN jabatan j ON j.id = p.jabatan_id AND j.deleted_at IS NULL
            LEFT JOIN unit_kerja uk ON uk.id = j.unit_kerja_id
            LEFT JOIN jenis_pegawai jp ON jp.id = p.jenis_pegawai_id
            WHERE p.user_id = ?
                AND p.deleted_at IS NULL
            LIMIT 1
        ', ['sudah terlaksana', 'belum terlaksana', 'sedang terlaksana', $userId]);

        if ($row === null) {
            return null;
        }

        $row->jumlah_diklat_selesai = (int) $row->jumlah_diklat_selesai;
        $row->jumlah_diklat_belum_selesai = (int) $row->jumlah_diklat_belum_selesai;
        $row->jabatan = (object) [
            'nama' => $row->jabatan_nama ?? null,
            'unitKerja' => (object) ['nama' => $row->unit_kerja_nama ?? null],
        ];
        $row->jenisPegawai = (object) ['nama' => $row->jenis_pegawai_nama ?? null];

        return $row;
    }

    public function getUnreadInfoNotificationsByUserId(int $userId): Collection
    {
        return $this->getNotificationsByUserId($userId, 'info', 'is_read = 0');
    }

    public function getActiveActionNotificationsByUserId(int $userId): Collection
    {
        return $this->getNotificationsByUserId($userId, 'action', 'is_resolved = 0');
    }

    public function getUpcomingDiklatByPegawaiId(int $pegawaiId): Collection
    {
        if ($pegawaiId <= 0) {
            return collect();
        }

        return collect(DB::select('
            SELECT
                ljd.id,
                ljd.status_diklat,
                d.nama_kegiatan,
                d.penyelenggara,
                d.tanggal_mulai,
                d.tanggal_selesai,
                d.tempat,
                d.waktu
            FROM list_jadwal_diklat ljd
            INNER JOIN diklat d ON d.id = ljd.diklat_id AND d.deleted_at IS NULL
            WHERE ljd.pegawai_id = ?
                AND ljd.status_diklat = ?
                AND ljd.deleted_at IS NULL
            ORDER BY d.tanggal_mulai ASC, ljd.id DESC
        ', [$pegawaiId, 'belum terlaksana']))->map(function ($row) {
            $row->diklat = (object) [
                'nama_kegiatan' => $row->nama_kegiatan ?? null,
                'penyelenggara' => $row->penyelenggara ?? null,
                'tanggal_mulai' => $this->dateOrNull($row->tanggal_mulai ?? null),
                'tanggal_selesai' => $this->dateOrNull($row->tanggal_selesai ?? null),
                'tempat' => $row->tempat ?? null,
                'waktu' => $this->dateTimeOrNull($row->waktu ?? null),
            ];

            return $row;
        });
    }

    private function getNotificationsByUserId(int $userId, string $type, string $extraWhere): Collection
    {
        return collect(DB::select("
            SELECT *
            FROM notification
            WHERE user_id = ?
                AND type = ?
                AND {$extraWhere}
            ORDER BY created_at DESC
        ", [$userId, $type]))->map(function ($row) {
            $row->is_read = (bool) $row->is_read;
            $row->is_resolved = (bool) $row->is_resolved;
            $row->action_payload = $row->action_payload ? (json_decode($row->action_payload, true) ?: []) : [];
            $row->created_at = $this->dateTimeOrNull($row->created_at ?? null);

            return $row;
        });
    }

    private function dateOrNull(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value)->startOfDay() : null;
    }

    private function dateTimeOrNull(mixed $value): ?Carbon
    {
        return $value ? Carbon::parse($value) : null;
    }
}
