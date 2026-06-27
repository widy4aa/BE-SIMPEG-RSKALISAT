<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keluarga\StoreOrangTuaRequest;
use App\Http\Requests\Keluarga\UpdateOrangTuaRequest;
use App\Services\Hrd\Keluarga\HrdOrangTuaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdOrangTuaController extends Controller
{
    public function __construct(private readonly HrdOrangTuaService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllOrangTua($id);

            return response()->json(['success' => true, 'message' => 'Data orang tua berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(StoreOrangTuaRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createOrangTua($id, $request->validated());

            return response()->json(['success' => true, 'message' => 'Data orang tua berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdateOrangTuaRequest $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->updateOrangTua($keluargaId, $id, $request->validated());

            return response()->json(['success' => true, 'message' => 'Data orang tua berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->deleteOrangTua($keluargaId, $id);

            return response()->json(['success' => true, 'message' => 'Data orang tua berhasil dihapus.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
