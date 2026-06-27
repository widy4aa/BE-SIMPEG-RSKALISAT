<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keluarga\StoreKontakDaruratRequest;
use App\Http\Requests\Keluarga\UpdateKontakDaruratRequest;
use App\Services\Hrd\HrdKeluargaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdKontakDaruratController extends Controller
{
    public function __construct(private readonly HrdKeluargaService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllKontakDarurat($id);

            return response()->json(['success' => true, 'message' => 'Data kontak darurat berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(StoreKontakDaruratRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createKontakDarurat($id, $request->validated());

            return response()->json(['success' => true, 'message' => 'Data kontak darurat berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdateKontakDaruratRequest $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->updateKontakDarurat($keluargaId, $id, $request->validated());

            return response()->json(['success' => true, 'message' => 'Data kontak darurat berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->deleteKontakDarurat($keluargaId, $id);

            return response()->json(['success' => true, 'message' => 'Data kontak darurat berhasil dihapus.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
