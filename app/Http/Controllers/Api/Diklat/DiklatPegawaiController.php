<?php

namespace App\Http\Controllers\Api\Diklat;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diklat\StorePegawaiDiklatRequest;
use App\Http\Requests\Diklat\UpdatePegawaiDiklatRequest;
use App\Services\Diklat\PegawaiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class DiklatPegawaiController extends Controller
{
    public function __construct(private readonly PegawaiService $pegawaiService) {}

    public function store(StorePegawaiDiklatRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $request->validated();
        $sertifFile = $request->file('upload_sertif');

        try {
            $result = $this->pegawaiService->create($userId, $payload, $sertifFile);
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
            $result = $this->pegawaiService->update($id, $userId, $payload, $sertifFile);
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
            $result = $this->pegawaiService->delete($id, $userId);
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
            $result = $this->pegawaiService->uploadLaporan($id, $userId, $payload, $laporanFile);
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
