<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StoreSipRequest;
use App\Http\Requests\RiwayatKarir\UpdateSipRequest;
use App\Services\Hrd\HrdRiwayatKarirService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdSipController extends Controller
{
    public function __construct(private readonly HrdRiwayatKarirService $service) {}

    public function index(Request $request, int $id): JsonResponse
    {
        try {
            return response()->json(['success' => true, 'message' => 'Data riwayat SIP berhasil diambil.', 'data' => $this->service->getSip($id)]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function store(StoreSipRequest $request, int $id): JsonResponse
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

    public function update(UpdateSipRequest $request, int $id, int $riwayatId): JsonResponse
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

    public function destroy(Request $request, int $id, int $riwayatId): JsonResponse
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
}
