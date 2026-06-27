<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StorePenugasanKlinisRequest;
use App\Http\Requests\RiwayatKarir\UpdatePenugasanKlinisRequest;
use App\Services\Hrd\HrdRiwayatKarirService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdPenugasanKlinisController extends Controller
{
    public function __construct(private readonly HrdRiwayatKarirService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat penugasan klinis berhasil diambil.', 'data' => $this->service->getPenugasanKlinis($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(StorePenugasanKlinisRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createPenugasanKlinis($id, $request->validated(), $request->file('dokumen_file'));

            return response()->json(['success' => true, 'message' => 'Riwayat penugasan klinis berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function update(UpdatePenugasanKlinisRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updatePenugasanKlinis($riwayatId, $id, $request->validated(), $request->file('dokumen_file'));

            return response()->json(['success' => true, 'message' => 'Riwayat penugasan klinis berhasil diperbarui.', 'data' => $result]);
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
            $result = $this->service->deletePenugasanKlinis($riwayatId, $id);

            return response()->json(['success' => true, 'message' => 'Riwayat penugasan klinis berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }
}
