<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Http\Requests\Keluarga\StoreAnakRequest;
use App\Http\Requests\Keluarga\StoreKontakDaruratRequest;
use App\Http\Requests\Keluarga\StoreOrangTuaRequest;
use App\Http\Requests\Keluarga\StorePasanganRequest;
use App\Http\Requests\Keluarga\UpdateAnakRequest;
use App\Http\Requests\Keluarga\UpdateKontakDaruratRequest;
use App\Http\Requests\Keluarga\UpdateOrangTuaRequest;
use App\Http\Requests\Keluarga\UpdatePasanganRequest;
use App\Services\Hrd\HrdKeluargaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class HrdKeluargaController extends Controller
{
    public function __construct(private readonly HrdKeluargaService $service) {}

    // ── Pasangan ──────────────────────────────────────────────────────────────

    public function indexPasangan(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllPasangan($id);
            return response()->json(['success' => true, 'message' => 'Data pasangan berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storePasangan(StorePasanganRequest $request, int $id): JsonResponse
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

    public function updatePasangan(UpdatePasanganRequest $request, int $id, int $keluargaId): JsonResponse
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

    public function destroyPasangan(Request $request, int $id, int $keluargaId): JsonResponse
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

    // ── Anak ──────────────────────────────────────────────────────────────────

    public function indexAnak(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllAnak($id);
            return response()->json(['success' => true, 'message' => 'Data anak berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeAnak(StoreAnakRequest $request, int $id): JsonResponse
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

    public function updateAnak(UpdateAnakRequest $request, int $id, int $keluargaId): JsonResponse
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

    public function destroyAnak(Request $request, int $id, int $keluargaId): JsonResponse
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

    // ── Orang Tua ─────────────────────────────────────────────────────────────

    public function indexOrangTua(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllOrangTua($id);
            return response()->json(['success' => true, 'message' => 'Data orang tua berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeOrangTua(StoreOrangTuaRequest $request, int $id): JsonResponse
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

    public function updateOrangTua(UpdateOrangTuaRequest $request, int $id, int $keluargaId): JsonResponse
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

    public function destroyOrangTua(Request $request, int $id, int $keluargaId): JsonResponse
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

    // ── Kontak Darurat ────────────────────────────────────────────────────────

    public function indexKontakDarurat(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllKontakDarurat($id);
            return response()->json(['success' => true, 'message' => 'Data kontak darurat berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeKontakDarurat(StoreKontakDaruratRequest $request, int $id): JsonResponse
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

    public function updateKontakDarurat(UpdateKontakDaruratRequest $request, int $id, int $keluargaId): JsonResponse
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

    public function destroyKontakDarurat(Request $request, int $id, int $keluargaId): JsonResponse
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

    // ── Tanggungan Lain ───────────────────────────────────────────────────────

    public function indexTanggunganLain(Request $request, int $id): JsonResponse
    {
        try {
            $data = $this->service->getAllTanggunganLain($id);
            return response()->json(['success' => true, 'message' => 'Data tanggungan lain berhasil diambil.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: '.$e->getMessage()], 500);
        }
    }

    public function storeTanggunganLain(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nama'              => ['required', 'string', 'max:255'],
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

    public function updateTanggunganLain(Request $request, int $id, int $keluargaId): JsonResponse
    {
        $validated = $request->validate([
            'nama'              => ['sometimes', 'string', 'max:255'],
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

    public function destroyTanggunganLain(Request $request, int $id, int $keluargaId): JsonResponse
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
