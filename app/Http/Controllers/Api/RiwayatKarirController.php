<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StoreJabatanRequest;
use App\Http\Requests\RiwayatKarir\StorePendidikanRequest;
use App\Http\Requests\RiwayatKarir\UpdateJabatanRequest;
use App\Http\Requests\RiwayatKarir\UpdatePendidikanRequest;
use App\Services\RiwayatKarir\JabatanService;
use App\Services\RiwayatKarir\PendidikanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RiwayatKarirController extends Controller
{
    public function __construct(
        private readonly PendidikanService $pendidikanService,
        private readonly JabatanService $jabatanService,
    ) {
    }

    public function pendidikan(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->pendidikanService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat pendidikan berhasil diambil.',
            'data' => $payload,
        ]);
    }

    public function jabatan(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->jabatanService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat jabatan berhasil diambil.',
            'data' => $payload,
        ]);
    }

    public function storeJabatan(StoreJabatanRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->jabatanService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_jabatan'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat jabatan berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updateJabatan(UpdateJabatanRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->jabatanService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_jabatan')
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat jabatan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat jabatan berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroyJabatan(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->jabatanService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat jabatan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat jabatan berhasil dihapus.',
        ]);
    }

    public function storePendidikan(StorePendidikanRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->pendidikanService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                ijazahFile: $request->file('ijazah'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pendidikan berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updatePendidikan(UpdatePendidikanRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->pendidikanService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                ijazahFile: $request->file('ijazah')
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat pendidikan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pendidikan berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroyPendidikan(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->pendidikanService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat pendidikan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pendidikan berhasil dihapus.',
        ]);
    }
}
