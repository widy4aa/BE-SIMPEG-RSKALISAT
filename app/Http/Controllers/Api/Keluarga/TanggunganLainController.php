<?php

namespace App\Http\Controllers\Api\Keluarga;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keluarga\StoreTanggunganLainRequest;
use App\Http\Requests\Keluarga\UpdateTanggunganLainRequest;
use App\Services\DataKeluarga\TanggunganLainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class TanggunganLainController extends Controller
{
    public function __construct(private readonly TanggunganLainService $service) {}

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $data = $this->service->getAllByUserId($userId);

            return response()->json([
                'success' => true,
                'message' => $data['welcome'],
                'data'    => $data['summary'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: '.$e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreTanggunganLainRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->service->createByUserId($userId, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data tanggungan lain berhasil ditambahkan.',
                'data'    => $result,
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateTanggunganLainRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->service->updateById($id, $userId, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data tanggungan lain berhasil diperbarui.',
                'data'    => $result,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->service->deleteById($id, $userId);

            return response()->json([
                'success' => true,
                'message' => 'Data tanggungan lain berhasil dihapus.',
                'data'    => $result,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: '.$e->getMessage(),
            ], 500);
        }
    }
}
