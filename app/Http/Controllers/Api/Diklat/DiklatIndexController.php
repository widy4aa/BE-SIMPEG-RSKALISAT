<?php

namespace App\Http\Controllers\Api\Diklat;

use App\Http\Controllers\Controller;
use App\Services\Diklat\DiklatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiklatIndexController extends Controller
{
    public function __construct(private readonly DiklatService $diklatService) {}

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->diklatService->getPayloadByRole($role, $userId, $request->query());

        if ($payload === null) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => $payload['welcome'],
            'data' => [
                'role' => $role,
                'diklat' => $payload['summary'],
            ],
        ]);
    }

    public function all(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');

        if ($role !== 'hrd' && $role !== 'direktur') {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak memiliki akses untuk melihat semua data diklat.',
            ], 403);
        }

        $diklat = $this->diklatService->getAllDiklat($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Data semua diklat berhasil diambil.',
            'data' => $diklat,
        ]);
    }
}
