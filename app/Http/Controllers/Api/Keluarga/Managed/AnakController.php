<?php

namespace App\Http\Controllers\Api\Keluarga\Managed;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keluarga\StoreAnakRequest;
use App\Http\Requests\Keluarga\UpdateAnakRequest;
use App\Services\Keluarga\Managed\AnakService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class AnakController extends Controller
{
    public function __construct(private readonly AnakService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllAnak($id);

            return response()->json(['success' => true, 'message' => 'Data anak berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(StoreAnakRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createAnak($id, $request->validated(), $request->file('akta_kelahiran_file'));

            return response()->json(['success' => true, 'message' => 'Data anak berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdateAnakRequest $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->updateAnak($keluargaId, $id, $request->validated(), $request->file('akta_kelahiran_file'));

            return response()->json(['success' => true, 'message' => 'Data anak berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->deleteAnak($keluargaId, $id);

            return response()->json(['success' => true, 'message' => 'Data anak berhasil dihapus.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
