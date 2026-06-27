<?php

namespace App\Http\Controllers\Api\Diklat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diklat\StoreHrdDiklatRequest;
use App\Http\Requests\Diklat\UpdateHrdDiklatRequest;
use App\Services\Diklat\DiklatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdDiklatController extends Controller
{
    public function __construct(private readonly DiklatService $diklatService) {}

    public function storeMaster(StoreHrdDiklatRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $request->validated();

        try {
            $result = $this->diklatService->createHrdDiklat(
                userId: $userId,
                payload: $payload,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Master Diklat berhasil dibuat.',
            'data' => $result,
        ], 201);
    }

    public function updateMaster(UpdateHrdDiklatRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $request->validated();

        try {
            $result = $this->diklatService->updateHrdDiklat(
                diklatId: $id,
                userId: $userId,
                payload: $payload,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Master Diklat berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function peserta(Request $request, int $id): JsonResponse
    {
        if ($id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID diklat wajib diisi dengan format angka.',
            ], 400);
        }

        $result = $this->diklatService->getPesertaDiklat($id);

        return response()->json([
            'success' => true,
            'message' => 'Data peserta diklat berhasil diambil.',
            'data' => $result,
        ]);
    }

    public function syncPeserta(Request $request, int $id): JsonResponse
    {
        if ($id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID diklat wajib diisi dengan format angka.',
            ], 400);
        }

        $pegawaiIds = $request->input('pegawai_ids', []);

        if (! is_array($pegawaiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter pegawai_ids harus berupa array.',
            ], 422);
        }

        $pegawaiIds = array_map('intval', $pegawaiIds);

        try {
            $result = $this->diklatService->syncPesertaDiklat($id, $pegawaiIds);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Peserta diklat berhasil diperbarui.',
            'data' => $result,
        ]);
    }

    public function menungguKelayakan(Request $request): JsonResponse
    {
        $result = $this->diklatService->getDiklatMenungguKelayakan();

        return response()->json([
            'success' => true,
            'message' => 'Data diklat menunggu kelayakan berhasil diambil.',
            'data' => $result,
        ]);
    }

    public function menungguValidasi(Request $request): JsonResponse
    {
        $result = $this->diklatService->getDiklatMenungguValidasi();

        return response()->json([
            'success' => true,
            'message' => 'Data diklat menunggu validasi berhasil diambil.',
            'data' => $result,
        ]);
    }

    public function updateStatusKelayakan(Request $request, int $id): JsonResponse
    {
        if ($id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID jadwal diklat wajib diisi dengan format angka.',
            ], 400);
        }

        $validated = $request->validate([
            'status_kelayakan' => ['required', 'boolean'],
        ]);

        try {
            $result = $this->diklatService->updateStatusKelayakan(
                jadwalId: $id,
                isLayak: (bool) $validated['status_kelayakan'],
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status kelayakan berhasil diperbarui.',
            'data' => $result,
        ]);
    }

    public function updateStatusValidasi(Request $request, int $id): JsonResponse
    {
        if ($id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID jadwal diklat wajib diisi dengan format angka.',
            ], 400);
        }

        $validated = $request->validate([
            'status_validasi' => ['required', 'boolean'],
        ]);

        try {
            $result = $this->diklatService->updateStatusValidasi(
                jadwalId: $id,
                isValid: (bool) $validated['status_validasi'],
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status validasi berhasil diperbarui.',
            'data' => $result,
        ]);
    }
}
