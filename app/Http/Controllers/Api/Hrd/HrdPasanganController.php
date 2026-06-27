<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keluarga\StorePasanganRequest;
use App\Http\Requests\Keluarga\UpdatePasanganRequest;
use App\Services\Hrd\HrdKeluargaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdPasanganController extends Controller
{
    public function __construct(private readonly HrdKeluargaService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllPasangan($id);

            return response()->json(['success' => true, 'message' => 'Data pasangan berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(StorePasanganRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createPasangan($id, $request->validated(), $request->file('buku_nikah_file'));

            return response()->json(['success' => true, 'message' => 'Data pasangan berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdatePasanganRequest $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->updatePasangan($keluargaId, $id, $request->validated(), $request->file('buku_nikah_file'));

            return response()->json(['success' => true, 'message' => 'Data pasangan berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, int $id, int $keluargaId): JsonResponse
    {
        try {
            $result = $this->service->deletePasangan($keluargaId, $id);

            return response()->json(['success' => true, 'message' => 'Data pasangan berhasil dihapus.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
