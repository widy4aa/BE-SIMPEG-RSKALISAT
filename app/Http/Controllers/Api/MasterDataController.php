<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GolonganRuang;
use App\Models\JenisBiaya;
use App\Models\JenisDiklat;
use App\Models\JenisPegawai;
use App\Models\JenisSip;
use App\Models\KategoriDiklat;
use App\Models\Profesi;
use App\Models\UnitKerja;
use Illuminate\Http\JsonResponse;

class MasterDataController extends Controller
{
    public function kategoriDiklat(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => KategoriDiklat::select('id', 'nama')->get(),
        ]);
    }

    public function tipeDiklat(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisDiklat::select('id', 'nama')->get(),
        ]);
    }

    public function jenisPegawai(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisPegawai::select('id', 'nama')->get(),
        ]);
    }

    public function unitKerja(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => UnitKerja::select('id', 'nama')->get(),
        ]);
    }

    public function jenisBiaya(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisBiaya::select('id', 'nama')->get(),
        ]);
    }

    public function golonganRuang(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => GolonganRuang::select('id', 'nama')->get(),
        ]);
    }

    public function profesi(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Profesi::select('id', 'nama')->get(),
        ]);
    }

    public function jenisSip(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisSip::select('id', 'nama')->get(),
        ]);
    }
}
