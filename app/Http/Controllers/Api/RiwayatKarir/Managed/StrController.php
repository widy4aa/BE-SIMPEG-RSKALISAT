<?php

namespace App\Http\Controllers\Api\RiwayatKarir\Managed;

use App\Http\Controllers\Controller;
use App\Services\RiwayatKarir\Managed\StrService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class StrController extends Controller
{
    public function __construct(private readonly StrService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getStr($id);
            return response()->json(['success' => true, 'message' => 'Data riwayat STR berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createStr($id, $request->validated(), $request->file("sk_str"));
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updateStr($riwayatId, $id, $request->validated(), $request->file("sk_str"));
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil diperbarui.', 'data' => $result]);
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
            $result = $this->service->deleteStr($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
