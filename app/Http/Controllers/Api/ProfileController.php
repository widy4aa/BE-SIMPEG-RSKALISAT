<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UploadKtpFileRequest;
use App\Http\Requests\Profile\UploadProfilePictureRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\Profile\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    public function show(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $payload = $this->profileService->getPayloadByRole($role, $userId);

        if ($payload === null) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => $payload['welcome'],
            'data' => [
                'role' => $role,
                'profile' => $payload['summary'],
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $validated = $request->validated();
        $note = isset($validated['note']) ? (string) $validated['note'] : null;
        unset($validated['note']);

        try {
            $agreement = $this->profileService->submitProfileUpdateWithAgreement(
                byUser: $userId,
                payload: $validated,
                note: $note,
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan perubahan profile berhasil dikirim dan menunggu persetujuan admin.',
            'data' => [
                'id_perubahan_data' => (int) $agreement->id,
                'status' => (string) $agreement->status,
                'fitur' => (string) $agreement->fitur,
                'jumlah_detail' => (int) $agreement->details->count(),
            ],
        ]);
    }

    public function updateProfilePicture(UploadProfilePictureRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->profileService->updateProfilePicture(
                $userId,
                $request->file('foto')
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto profile berhasil diupdate.',
            'data' => $result,
        ]);
    }

    public function uploadKtp(UploadKtpFileRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        try {
            $result = $this->profileService->updateKtpFile(
                $userId,
                $request->file('ktp')
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'File KTP berhasil diupload.',
            'data' => $result,
        ]);
    }
}
