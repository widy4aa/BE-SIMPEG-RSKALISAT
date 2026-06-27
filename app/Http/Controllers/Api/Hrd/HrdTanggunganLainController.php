<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Services\Hrd\HrdKeluargaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdTanggunganLainController extends Controller
{
    public function __construct(private readonly HrdKeluargaService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllTanggunganLain($id);

            return response()->json(['success' => true, 'message' => 'Data tanggungan lain berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'hubungan_keluarga' => ['required', 'string', 'max:100'],
            'status_tanggungan' => ['sometimes', 'nullable', 'boolean'],
        ]);

        try {
            $result = $this->service->createTanggunganLain($id, $validated);

            return response()->json(['success' => true, 'message' => 'Data tanggungan lain berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id, int $keluargaId): JsonResponse
    {
        $validated = $request->validate([
            'nama' => ['sometimes', 'string', 'max:255'],
            'hubungan_keluarga' => ['sometimes', 'string', 'max:100'],
            'status_tanggungan' => ['sometimes', 'nullable', 'boolean'],
        ]);

        try {
            $result = $this->service->updateTanggunganLain($keluargaId, $id, $validated);

            return response()->json(['success' => true, 'message' => 'Data tanggungan lain berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->deleteTanggunganLain($keluargaId, $id);

            return response()->json(['success' => true, 'message' => 'Data tanggungan lain berhasil dihapus.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
