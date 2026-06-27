<?php

namespace App\Http\Controllers\Api\RiwayatKarir\Managed;

use App\Http\Controllers\Controller;
use App\Services\RiwayatKarir\Managed\JabatanService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class JabatanController extends Controller
{
    public function __construct(private readonly JabatanService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getJabatan($id);
            return response()->json(['success' => true, 'message' => 'Data riwayat jabatan berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createJabatan($id, $request->validated(), $request->file("sk_jabatan"));
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updateJabatan($riwayatId, $id, $request->validated(), $request->file("sk_jabatan"));
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil diperbarui.', 'data' => $result]);
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
            $result = $this->service->deleteJabatan($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat jabatan berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
