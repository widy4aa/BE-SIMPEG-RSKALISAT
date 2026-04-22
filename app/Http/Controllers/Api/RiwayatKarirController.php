<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RiwayatKarir\StoreJabatanRequest;
use App\Http\Requests\RiwayatKarir\StorePangkatRequest;
use App\Http\Requests\RiwayatKarir\StorePendidikanRequest;
use App\Http\Requests\RiwayatKarir\StorePenugasanKlinisRequest;
use App\Http\Requests\RiwayatKarir\StoreSipRequest;
use App\Http\Requests\RiwayatKarir\StoreStrRequest;
use App\Http\Requests\RiwayatKarir\UpdateJabatanRequest;
use App\Http\Requests\RiwayatKarir\UpdatePangkatRequest;
use App\Http\Requests\RiwayatKarir\UpdatePendidikanRequest;
use App\Http\Requests\RiwayatKarir\UpdatePenugasanKlinisRequest;
use App\Http\Requests\RiwayatKarir\UpdateSipRequest;
use App\Http\Requests\RiwayatKarir\UpdateStrRequest;
use App\Services\RiwayatKarir\JabatanService;
use App\Services\RiwayatKarir\PangkatService;
use App\Services\RiwayatKarir\PendidikanService;
use App\Services\RiwayatKarir\PenugasanKlinisService;
use App\Services\RiwayatKarir\SipService;
use App\Services\RiwayatKarir\StrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class RiwayatKarirController extends Controller
{
    public function __construct(
        private readonly PendidikanService $pendidikanService,
        private readonly JabatanService $jabatanService,
        private readonly PangkatService $pangkatService,
        private readonly SipService $sipService,
        private readonly StrService $strService,
        private readonly PenugasanKlinisService $penugasanKlinisService,
    ) {
    }

    public function pendidikan(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->pendidikanService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat pendidikan berhasil diambil.',
            'data' => $payload,
        ]);
    }

    public function jabatan(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->jabatanService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat jabatan berhasil diambil.',
            'data' => $payload,
        ]);
    }

    public function storeJabatan(StoreJabatanRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->jabatanService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_jabatan'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat jabatan berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updateJabatan(UpdateJabatanRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->jabatanService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_jabatan')
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat jabatan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat jabatan berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroyJabatan(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->jabatanService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat jabatan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat jabatan berhasil dihapus.',
        ]);
    }

    public function storePendidikan(StorePendidikanRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->pendidikanService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                ijazahFile: $request->file('ijazah'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pendidikan berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updatePendidikan(UpdatePendidikanRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->pendidikanService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                ijazahFile: $request->file('ijazah')
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat pendidikan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pendidikan berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroyPendidikan(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->pendidikanService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat pendidikan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pendidikan berhasil dihapus.',
        ]);
    }

    public function pangkat(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $result = $this->pangkatService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat pangkat.',
            'data' => $result,
        ]);
    }

    public function storePangkat(StorePangkatRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->pangkatService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_pangkat'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pangkat berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updatePangkat(UpdatePangkatRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->pangkatService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_pangkat'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat pangkat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pangkat berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroyPangkat(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->pangkatService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat pangkat tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat pangkat berhasil dihapus.',
        ]);
    }

    public function sip(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $result = $this->sipService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat SIP.',
            'data' => $result,
        ]);
    }

    public function storeSip(StoreSipRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->sipService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_sip'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat SIP berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updateSip(UpdateSipRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->sipService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_sip'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat SIP tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat SIP berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroySip(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->sipService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat SIP tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat SIP berhasil dihapus.',
        ]);
    }

    public function str(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $result = $this->strService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat STR.',
            'data' => $result,
        ]);
    }

    public function storeStr(StoreStrRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->strService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_str'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat STR berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updateStr(UpdateStrRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->strService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('sk_str'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat STR tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat STR berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroyStr(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->strService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat STR tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat STR berhasil dihapus.',
        ]);
    }

    public function penugasanKlinis(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $result = $this->penugasanKlinisService->getByUserId($userId);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil riwayat penugasan klinis.',
            'data' => $result,
        ]);
    }

    public function storePenugasanKlinis(StorePenugasanKlinisRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->penugasanKlinisService->createByUserId(
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('dokumen_file'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat penugasan klinis berhasil ditambahkan.',
            'data' => $result,
        ], 201);
    }

    public function updatePenugasanKlinis(UpdatePenugasanKlinisRequest $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->penugasanKlinisService->updateByIdAndUserId(
                id: $id,
                userId: $userId,
                payload: $request->validated(),
                skFile: $request->file('dokumen_file'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat penugasan klinis tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat penugasan klinis berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function destroyPenugasanKlinis(Request $request, int $id): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $this->penugasanKlinisService->deleteByIdAndUserId($id, $userId);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data riwayat penugasan klinis tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Riwayat penugasan klinis berhasil dihapus.',
        ]);
    }
}
