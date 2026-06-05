<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diklat\StoreHrdDiklatRequest;
use App\Http\Requests\Diklat\UpdateHrdDiklatRequest;
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

        if (!is_array($pegawaiIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter pegawai_ids harus berupa array.',
            ], 422);
        }

        // Clean array
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
    public function uploadLaporan(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $request->validate([
            'upload_laporan' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'no_sertif' => ['nullable', 'string', 'max:255'],
        ]);

        $payload = $request->only('no_sertif');
        $laporanFile = $request->file('upload_laporan');

        try {
            $result = $this->diklatService->uploadLaporanPegawai(
                diklatId: $id,
                userId: $userId,
                payload: $payload,
                laporanFile: $laporanFile,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil diupload/diedit.',
            'data' => $result,
        ]);
    }
}
