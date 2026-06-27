<?php

namespace App\Http\Controllers\Api\Diklat\Self;

use App\Http\Controllers\Controller;
use App\Services\Diklat\PegawaiDiklatListService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiklatIndexController extends Controller
{
    public function __construct(
        private readonly PegawaiDiklatListService $pegawaiService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->pegawaiService->build($userId, $request->query());

        return response()->json([
            'success' => true,
            'message' => $payload['welcome'],
            'data'    => [
                'role'   => 'pegawai',
                'diklat' => $payload['summary'],
            ],
        ]);
    }
}
