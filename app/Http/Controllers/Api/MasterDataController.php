<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GolonganRuang;
use App\Models\JenisBiaya;
use App\Models\JenisDiklat;
use App\Models\JenisPegawai;
use App\Models\JenisSip;
use App\Models\KategoriDiklat;
use App\Models\Profesi;
use App\Models\UnitKerja;
use App\Services\MasterData\MasterDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class MasterDataController extends Controller
{
    public function __construct(private readonly MasterDataService $masterDataService)
    {
    }

    public function kategoriDiklat(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => KategoriDiklat::select('id', 'nama')->get(),
        ]);
    }

    public function tipeDiklat(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisDiklat::select('id', 'nama')->get(),
        ]);
    }

    public function jenisPegawai(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisPegawai::select('id', 'nama')->get(),
        ]);
    }

    public function unitKerja(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => UnitKerja::select('id', 'nama')->get(),
        ]);
    }

    public function jenisBiaya(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisBiaya::select('id', 'nama')->get(),
        ]);
    }

    public function golonganRuang(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => GolonganRuang::select('id', 'nama')->get(),
        ]);
    }

    public function profesi(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Profesi::select('id', 'nama')->get(),
        ]);
    }

    public function jenisSip(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => JenisSip::select('id', 'nama')->get(),
        ]);
    }

    public function storeKategoriDiklat(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'kategori-diklat');
    }

    public function updateKategoriDiklat(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'kategori-diklat');
    }

    public function destroyKategoriDiklat(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'kategori-diklat');
    }

    public function storeTipeDiklat(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'tipe-diklat');
    }

    public function updateTipeDiklat(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'tipe-diklat');
    }

    public function destroyTipeDiklat(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'tipe-diklat');
    }

    public function storeJenisPegawai(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'jenis-pegawai');
    }

    public function updateJenisPegawai(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'jenis-pegawai');
    }

    public function destroyJenisPegawai(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'jenis-pegawai');
    }

    public function storeUnitKerja(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'unit-kerja');
    }

    public function updateUnitKerja(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'unit-kerja');
    }

    public function destroyUnitKerja(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'unit-kerja');
    }

    public function storeJenisBiaya(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'jenis-biaya');
    }

    public function updateJenisBiaya(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'jenis-biaya');
    }

    public function destroyJenisBiaya(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'jenis-biaya');
    }

    public function storeGolonganRuang(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'golongan-ruang');
    }

    public function updateGolonganRuang(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'golongan-ruang');
    }

    public function destroyGolonganRuang(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'golongan-ruang');
    }

    public function storeProfesi(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'profesi');
    }

    public function updateProfesi(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'profesi');
    }

    public function destroyProfesi(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'profesi');
    }

    public function storeJenisSip(Request $request): JsonResponse
    {
        return $this->handleCreate($request, 'jenis-sip');
    }

    public function updateJenisSip(Request $request, int $id): JsonResponse
    {
        return $this->handleUpdate($request, $id, 'jenis-sip');
    }

    public function destroyJenisSip(int $id): JsonResponse
    {
        return $this->handleDelete($id, 'jenis-sip');
    }

    private function handleCreate(Request $request, string $type): JsonResponse
    {
        $payload = $this->validateMasterData($request, $type, null);

        try {
            $result = $this->masterDataService->create($type, $payload);
        } catch (InvalidArgumentException $exception) {
            return $this->handleServiceException($exception);
        }

        $label = $this->masterDataService->getLabel($type);

        return response()->json([
            'success' => true,
            'message' => $label . ' berhasil dibuat.',
            'data' => $result,
        ], 201);
    }

    private function handleUpdate(Request $request, int $id, string $type): JsonResponse
    {
        if ($id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID wajib diisi dengan format angka.',
            ], 400);
        }

        $payload = $this->validateMasterData($request, $type, $id);

        try {
            $result = $this->masterDataService->update($type, $id, $payload);
        } catch (InvalidArgumentException $exception) {
            return $this->handleServiceException($exception);
        }

        $label = $this->masterDataService->getLabel($type);

        return response()->json([
            'success' => true,
            'message' => $label . ' berhasil diperbarui.',
            'data' => $result,
        ]);
    }

    private function handleDelete(int $id, string $type): JsonResponse
    {
        if ($id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter ID wajib diisi dengan format angka.',
            ], 400);
        }

        try {
            $this->masterDataService->delete($type, $id);
        } catch (InvalidArgumentException $exception) {
            return $this->handleServiceException($exception);
        }

        $label = $this->masterDataService->getLabel($type);

        return response()->json([
            'success' => true,
            'message' => $label . ' berhasil dihapus.',
            'data' => [
                'deleted_id' => $id,
            ],
        ]);
    }

    private function validateMasterData(Request $request, string $type, ?int $id): array
    {
        $rules = $this->masterDataService->getValidationRules($type, $id);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422));
        }

        return $validator->validated();
    }

    private function handleServiceException(InvalidArgumentException $exception): JsonResponse
    {
        $status = $exception->getCode() > 0 ? $exception->getCode() : 422;

        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
        ], $status);
    }
}
