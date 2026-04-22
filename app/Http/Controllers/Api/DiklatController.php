<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diklat\StorePegawaiDiklatRequest;
use App\Http\Requests\Diklat\UpdatePegawaiDiklatRequest;
use App\Services\Diklat\DiklatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class DiklatController extends Controller
{
    public function __construct(private readonly DiklatService $diklatService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->diklatService->getPayloadByRole($role, $userId);

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

    public function store(StorePegawaiDiklatRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $request->validated();
        $sertifFile = $request->file('upload_sertif');

        try {
            $result = $this->diklatService->createPegawaiDiklat(
                userId: $userId,
                payload: $payload,
                sertifFile: $sertifFile,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Diklat berhasil dibuat.',
            'data' => $result,
        ], 201);
    }

    public function update(UpdatePegawaiDiklatRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $request->validated();
        $sertifFile = $request->file('upload_sertif');

        try {
            $result = $this->diklatService->updatePegawaiDiklat(
                diklatId: $id,
                userId: $userId,
                payload: $payload,
                sertifFile: $sertifFile,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Diklat berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->diklatService->deletePegawaiDiklat(
                diklatId: $id,
                userId: $userId,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Diklat berhasil dihapus.',
            'data' => $result,
        ]);
    }
}
