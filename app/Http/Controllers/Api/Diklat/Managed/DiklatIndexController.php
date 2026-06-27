<?php

namespace App\Http\Controllers\Api\Diklat\Managed;

use App\Http\Controllers\Controller;
use App\Services\Diklat\AdminDiklatSummaryService;
use App\Services\Diklat\DirekturDiklatSummaryService;
use App\Services\Diklat\HrdDiklatListService;
use App\Services\Diklat\PegawaiDiklatListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiklatIndexController extends Controller
{
    public function __construct(
        private readonly AdminDiklatSummaryService $adminService,
        private readonly HrdDiklatListService $hrdService,
        private readonly DirekturDiklatSummaryService $direkturService,
        private readonly PegawaiDiklatListService $pegawaiService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role   = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = match ($role) {
            'admin'    => $this->adminService->build($userId),
            'hrd'      => $this->hrdService->build($userId, $request->query()),
            'direktur' => $this->direkturService->build($userId, $request->query()),
            'pegawai'  => $this->pegawaiService->build($userId, $request->query()),
            default    => null,
        };

        if ($payload === null) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => $payload['welcome'],
            'data'    => ['role' => $role, 'diklat' => $payload['summary']],
        ]);
    }

    public function all(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role   = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');

        if ($role !== 'hrd' && $role !== 'direktur') {
            return response()->json(['success' => false, 'message' => 'Role tidak memiliki akses untuk melihat semua data diklat.'], 403);
        }

        $diklat = $this->hrdService->getAllDiklat($request->query());

        return response()->json(['success' => true, 'message' => 'Data semua diklat berhasil diambil.', 'data' => $diklat]);
    }
}
