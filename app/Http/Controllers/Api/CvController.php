<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Generate\CvService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CvController extends Controller
{
    public function __construct(
        private readonly CvService $cvService
    ) {
    }

    public function generate(Request $request): JsonResponse
    {
        $claims = $request->attributes->get('_jwt_claims', []);
        $userId = (int) ($claims['sub'] ?? 0);
        $role = strtolower((string) ($claims['role'] ?? ''));
        
        $pegawaiId = $request->query('pegawai_id') ? (int) $request->query('pegawai_id') : null;

        try {
            $payload = $this->cvService->generateCvData($userId, $role, $pegawaiId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data CV berhasil diambil.',
            'data' => $payload,
        ]);
    }
}
