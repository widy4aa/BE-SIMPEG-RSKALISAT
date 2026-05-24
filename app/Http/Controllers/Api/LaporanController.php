<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(private readonly \App\Services\Diklat\HrdService $hrdService)
    {
    }

    public function laporanDiklat(Request $request): \Illuminate\Http\JsonResponse
    {
        $bulanAwal = $request->input('bulan_awal');
        $tahunAwal = $request->input('tahun_awal');
        $bulanAkhir = $request->input('bulan_akhir');
        $tahunAkhir = $request->input('tahun_akhir');

        if (!$bulanAwal || !$tahunAwal || !$bulanAkhir || !$tahunAkhir) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter bulan_awal, tahun_awal, bulan_akhir, dan tahun_akhir wajib diisi.',
            ], 400);
        }

        if (!is_numeric($bulanAwal) || !is_numeric($tahunAwal) || !is_numeric($bulanAkhir) || !is_numeric($tahunAkhir) || 
            $bulanAwal < 1 || $bulanAwal > 12 || $bulanAkhir < 1 || $bulanAkhir > 12) {
            return response()->json([
                'success' => false,
                'message' => 'Format parameter bulan (1-12) dan tahun tidak valid.',
            ], 400);
        }

        $result = $this->hrdService->generateLaporanDiklat((int) $bulanAwal, (int) $tahunAwal, (int) $bulanAkhir, (int) $tahunAkhir);

        return response()->json([
            'success' => true,
            'message' => 'Data rekap diklat berhasil diambil.',
            'data' => $result,
        ]);
    }
}
