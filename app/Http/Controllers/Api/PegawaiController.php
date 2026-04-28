<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Pegawai\PegawaiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function __construct(
        private readonly PegawaiService $pegawaiService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $claims = $request->attributes->get('_jwt_claims', []);
        $role = strtolower((string) ($claims['role'] ?? ''));

        $payload = $this->pegawaiService->getPayloadByRole($role);

        if (!$payload) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak memiliki akses ke data pegawai.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data pegawai berhasil diambil',
            'data' => $payload,
        ]);
    }
}
