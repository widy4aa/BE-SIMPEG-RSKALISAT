<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StorePangkatRequest;
use App\Http\Requests\RiwayatKarir\StorePenugasanKlinisRequest;
use App\Http\Requests\RiwayatKarir\StoreSipRequest;
use App\Http\Requests\RiwayatKarir\StoreStrRequest;
use App\Http\Requests\RiwayatKarir\UpdatePangkatRequest;
use App\Http\Requests\RiwayatKarir\UpdatePenugasanKlinisRequest;
use App\Http\Requests\RiwayatKarir\UpdateSipRequest;
use App\Http\Requests\RiwayatKarir\UpdateStrRequest;
use App\Services\Hrd\HrdRiwayatKarirService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdRiwayatKarirController extends Controller
{
    public function __construct(private readonly HrdRiwayatKarirService $service) {}

    // ── STR ───────────────────────────────────────────────────────────────────

    public function str(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat STR berhasil diambil.', 'data' => $this->service->getStr($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeStr(StoreStrRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createStr($id, $request->validated(), $request->file('sk_str'));
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function updateStr(UpdateStrRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updateStr($riwayatId, $id, $request->validated(), $request->file('sk_str'));
            return response()->json(['success' => true, 'message' => 'Riwayat STR berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroyStr(Request $request, int $id, int $riwayatId): JsonResponse
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

    // ── SIP ───────────────────────────────────────────────────────────────────

    public function sip(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat SIP berhasil diambil.', 'data' => $this->service->getSip($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeSip(StoreSipRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->createSip($id, $request->validated(), $request->file('sk_sip'));
            return response()->json(['success' => true, 'message' => 'Riwayat SIP berhasil ditambahkan.', 'data' => $result], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function updateSip(UpdateSipRequest $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->updateSip($riwayatId, $id, $request->validated(), $request->file('sk_sip'));
            return response()->json(['success' => true, 'message' => 'Riwayat SIP berhasil diperbarui.', 'data' => $result]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function destroySip(Request $request, int $id, int $riwayatId): JsonResponse
    {
        try {
            $result = $this->service->deleteSip($riwayatId, $id);
            return response()->json(['success' => true, 'message' => 'Riwayat SIP berhasil dihapus.', 'data' => $result]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    // ── Penugasan Klinis ──────────────────────────────────────────────────────

    public function penugasanKlinis(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat penugasan klinis berhasil diambil.', 'data' => $this->service->getPenugasanKlinis($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storePenugasanKlinis(StorePenugasanKlinisRequest $request, int $id): JsonResponse
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

    public function updatePenugasanKlinis(UpdatePenugasanKlinisRequest $request, int $id, int $riwayatId): JsonResponse
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

    public function destroyPenugasanKlinis(Request $request, int $id, int $riwayatId): JsonResponse
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

    // ── Pangkat ───────────────────────────────────────────────────────────────

    public function pangkat(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat pangkat berhasil diambil.', 'data' => $this->service->getPangkat($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storePangkat(StorePangkatRequest $request, int $id): JsonResponse
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

    public function updatePangkat(UpdatePangkatRequest $request, int $id, int $riwayatId): JsonResponse
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

    public function destroyPangkat(Request $request, int $id, int $riwayatId): JsonResponse
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
