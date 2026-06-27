<?php

namespace App\Http\Controllers\Api\RiwayatKarir\Managed;

use App\Http\Controllers\Controller;
use App\Services\RiwayatKarir\Managed\PendidikanService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PendidikanController extends Controller
{
    public function __construct(private readonly PendidikanService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getPendidikan($id);
            return response()->json(['success' => true, 'message' => 'Data riwayat pendidikan berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createPendidikan($id, $request->validated(), $request->file("ijazah"));
            return response()->json(['success' => true, 'message' => 'Riwayat pendidikan berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updatePendidikan($riwayatId, $id, $request->validated(), $request->file("ijazah"));
            return response()->json(['success' => true, 'message' => 'Riwayat pendidikan berhasil diperbarui.', 'data' => $result]);
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
            $result = $this->service->deletePendidikan($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat pendidikan berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
