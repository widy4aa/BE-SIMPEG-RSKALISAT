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
        $claims = $request->input('_jwt_claims', []);
        $role = strtolower((string) (is_array($claims) ? ($claims['role'] ?? '') : ''));

        $payload = $this->pegawaiService->getPayloadByRole($role, $request->query());

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

    public function show(int $id): JsonResponse
    {
        try {
            $payload = $this->pegawaiService->getPegawaiDetailData($id);
            return response()->json([
                'success' => true,
                'message' => 'Detail data pegawai berhasil diambil',
                'data' => $payload,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function store(\App\Http\Requests\Pegawai\StorePegawaiRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role = strtolower((string) (is_array($claims) ? ($claims['role'] ?? '') : ''));

        $result = $this->pegawaiService->createPegawai($role, $request->validated());

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak memiliki akses untuk membuat data pegawai.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pegawai berhasil ditambahkan',
            'data' => $result,
        ], 201);
    }

    public function changeRole(int $id, \App\Http\Requests\Pegawai\ChangeRoleRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $adminRole = strtolower((string) (is_array($claims) ? ($claims['role'] ?? '') : ''));
        $adminUserId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->pegawaiService->changeRole($adminRole, $id, $request->validated(), $adminUserId);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role tidak memiliki akses untuk mengubah role/status pegawai.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role pegawai berhasil diubah',
                'data' => $result,
            ], 200);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
