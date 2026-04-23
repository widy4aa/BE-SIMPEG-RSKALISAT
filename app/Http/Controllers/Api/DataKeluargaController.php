<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataKeluarga\DataKeluargaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DataKeluargaController extends Controller
{
    public function __construct(private readonly DataKeluargaService $service) {}

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $data = $this->service->getDataKeluargaSummaryByUserId($userId);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data keluarga',
                'data' => $data,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }
    }
}
