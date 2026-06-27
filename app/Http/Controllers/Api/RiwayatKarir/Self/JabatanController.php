<?php

namespace App\Http\Controllers\Api\RiwayatKarir\Self;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StoreJabatanRequest;
use App\Http\Requests\RiwayatKarir\UpdateJabatanRequest;
use App\Services\RiwayatKarir\Self\JabatanService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class JabatanController extends Controller
{
    public function __construct(private readonly JabatanService $service) {}

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->service->getByUserId($userId);
        return response()->json(['success' => true, 'message' => 'Data riwayat jabatan berhasil diambil.', 'data' => $payload]);
    }

    public function store(StoreJabatanRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->service->createByUserId(userId: $userId, payload: $request->validated(), skFile: $request->file('sk_jabatan'));
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdateJabatanRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->service->updateByIdAndUserId(id: $id, userId: $userId, payload: $request->validated(), skFile: $request->file('sk_jabatan'));
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data riwayat jabatan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->service->deleteByIdAndUserId($id, $userId);
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil dihapus.']);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data riwayat jabatan tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
