<?php

namespace App\Http\Controllers\Api\RiwayatKarir\Self;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StorePangkatRequest;
use App\Http\Requests\RiwayatKarir\UpdatePangkatRequest;
use App\Services\RiwayatKarir\Self\PangkatService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PangkatController extends Controller
{
    public function __construct(private readonly PangkatService $service) {}

    public function index(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $result = $this->service->getByUserId($userId);
        return response()->json(['success' => true, 'message' => 'Berhasil mengambil riwayat pangkat.', 'data' => $result]);
    }

    public function store(StorePangkatRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->service->createByUserId(userId: $userId, payload: $request->validated(), skFile: $request->file('sk_pangkat'));
            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdatePangkatRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->service->updateByIdAndUserId(id: $id, userId: $userId, payload: $request->validated(), skFile: $request->file('sk_pangkat'));
            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data riwayat pangkat tidak ditemukan.'], 404);
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
            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil dihapus.']);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Data riwayat pangkat tidak ditemukan.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
