<?php

namespace App\Http\Controllers\Api\Diklat\Self;

use App\Http\Controllers\Controller;
use App\Http\Requests\Diklat\StorePegawaiDiklatRequest;
use App\Http\Requests\Diklat\UpdatePegawaiDiklatRequest;
use App\Services\Diklat\PegawaiDiklatLaporanService;
use App\Services\Diklat\PegawaiDiklatMutationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class DiklatController extends Controller
{
    public function __construct(
        private readonly PegawaiDiklatMutationService $mutationService,
        private readonly PegawaiDiklatLaporanService $laporanService,
    ) {}

    public function store(StorePegawaiDiklatRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $request->validated();
        $sertifFile = $request->file('upload_sertif');

        try {
            $result = $this->mutationService->create($userId, $payload, $sertifFile);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Diklat berhasil dibuat.', 'data' => $result], 201);
    }

    public function update(UpdatePegawaiDiklatRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $request->validated();
        $sertifFile = $request->file('upload_sertif');

        try {
            $result = $this->mutationService->update($id, $userId, $payload, $sertifFile);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Diklat berhasil diupdate.', 'data' => $result]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->mutationService->delete($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Diklat berhasil dihapus.', 'data' => $result]);
    }

    public function uploadLaporan(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $request->validate([
            'upload_laporan' => ['required_without:upload_sertif', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'upload_sertif'  => ['required_without:upload_laporan', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'no_sertif'      => ['required', 'string', 'max:255'],
        ]);

        $payload     = $request->only('no_sertif');
        $laporanFile = $request->file('upload_laporan') ?? $request->file('upload_sertif');

        try {
            $result = $this->laporanService->uploadLaporan($id, $userId, $payload, $laporanFile);
        } catch (InvalidArgumentException $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Laporan berhasil diupload/diedit.', 'data' => $result]);
    }
}
