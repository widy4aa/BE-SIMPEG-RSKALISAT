<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StorePangkatRequest;
use App\Http\Requests\RiwayatKarir\UpdatePangkatRequest;
use App\Services\Hrd\RiwayatKarir\HrdPangkatService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdPangkatController extends Controller
{
    public function __construct(private readonly HrdPangkatService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat pangkat berhasil diambil.', 'data' => $this->service->getPangkat($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(StorePangkatRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createPangkat($id, $request->validated(), $request->file('sk_pangkat'));

            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdatePangkatRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updatePangkat($riwayatId, $id, $request->validated(), $request->file('sk_pangkat'));

            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->deletePangkat($riwayatId, $id);

            return response()->json(['success' => true, 'message' => 'Riwayat pangkat berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
