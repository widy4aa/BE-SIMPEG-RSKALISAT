<?php

namespace App\Repositories\Dashboard;

use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HrdDashboardRepository
{
    public function getDashboardStats(): array
    {
        $totalPegawai = Pegawai::count();

        // Pegawai kurang lengkap: 
        // Either pribadi doesn't exist, or has null in important fields, or pegawai has null in important fields.
        $totalKurangLengkap = Pegawai::whereNull('nip')
            ->orWhereNull('jenis_pegawai_id')
            ->orWhereNull('profesi_id')
            ->orWhereNull('tgl_masuk')
            ->orWhereDoesntHave('pribadi')
            ->orWhereHas('pribadi', function ($query) {
                $query->whereNull('tanggal_lahir')
                    ->orWhereNull('jenis_kelamin')
                    ->orWhereNull('agama')
                    ->orWhereNull('alamat')
                    ->orWhereNull('no_telp')
                    ->orWhereNull('pendidikan_terakhir');
            })
            ->count();

        $totalLengkap = max(0, $totalPegawai - $totalKurangLengkap);

        // Group by jenis_pegawai
        $jenisPegawaiData = DB::table('pegawai')
            ->join('jenis_pegawai', 'pegawai.jenis_pegawai_id', '=', 'jenis_pegawai.id')
            ->select('jenis_pegawai.nama as label', DB::raw('count(*) as value'))
            ->groupBy('jenis_pegawai.id', 'jenis_pegawai.nama')
            ->get()
            ->pluck('value', 'label')
            ->toArray();

        // Group by profesi
        $profesiData = DB::table('pegawai')
            ->join('profesi', 'pegawai.profesi_id', '=', 'profesi.id')
            ->select('profesi.nama as label', DB::raw('count(*) as value'))
            ->groupBy('profesi.id', 'profesi.nama')
            ->get()
            ->pluck('value', 'label')
            ->toArray();

        // Group by tingkat pendidikan terakhir
        $pendidikanData = DB::table('pegawai_pribadi')
            ->whereNotNull('pendidikan_terakhir')
            ->select('pendidikan_terakhir as label', DB::raw('count(*) as value'))
            ->groupBy('pendidikan_terakhir')
            ->get()
            ->pluck('value', 'label')
            ->toArray();

        // Tahun masuk 5 tahun terakhir
        $currentYear = (int) date('Y');
        $fiveYearsAgo = $currentYear - 4; // 5 years including current year (e.g. 2022-2026)

        $tahunMasukCounts = DB::table('pegawai')
            ->whereNotNull('tgl_masuk')
            ->whereYear('tgl_masuk', '>=', $fiveYearsAgo)
            ->select(DB::raw('YEAR(tgl_masuk) as year'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('YEAR(tgl_masuk)'))
            ->get()
            ->pluck('count', 'year')
            ->toArray();

        // Ensure all 5 years are present in the array even if 0
        $tahunMasuk = [];
        for ($i = $fiveYearsAgo; $i <= $currentYear; $i++) {
            $tahunMasuk[(string)$i] = $tahunMasukCounts[$i] ?? 0;
        }

        return [
            'pegawai' => [
                'total_pegawai' => $totalPegawai,
                'total_pegawai_kurang_lengkap' => $totalKurangLengkap,
                'total_pegawai_lengkap' => $totalLengkap,
                'jenis_pegawai' => $jenisPegawaiData,
                'profesi' => $profesiData,
                'tingkat_pendidikan' => $pendidikanData,
                'tahun_masuk_5_tahun_terakhir' => $tahunMasuk,
            ],
            'diklat_asn' => [
                'total_diklat' => 12,
                'selesai' => 8,
                'berlangsung' => 4,
            ],
            'diklat_tenkes' => [
                'total_diklat' => 20,
                'selesai' => 15,
                'berlangsung' => 5,
            ],
        ];
    }
}
