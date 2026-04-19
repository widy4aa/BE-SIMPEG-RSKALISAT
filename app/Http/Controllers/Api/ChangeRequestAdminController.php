<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChangeRequest\ChangeRequestAdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ChangeRequestAdminController extends Controller
{
    public function __construct(private readonly ChangeRequestAdminService $changeRequestAdminService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $fitur = $request->query('fitur');

        $result = $this->changeRequestAdminService->list(
            is_string($status) ? $status : null,
            is_string($fitur) ? $fitur : null,
        );

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengajuan perubahan data berhasil diambil.',
            'data' => $result,
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $result = $this->changeRequestAdminService->detail($id);

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan perubahan data tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pengajuan perubahan data berhasil diambil.',
            'data' => $result,
        ]);
    }

    public function accept(Request $request, int $id): JsonResponse
    {
        $note = $request->input('note');
        $adminNote = is_string($note) ? $note : null;

        try {
            $result = $this->changeRequestAdminService->accept($id, $adminNote);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan perubahan data tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan perubahan data berhasil disetujui.',
            'data' => $result,
        ]);
    }

    public function reject(Request $request, int $id): JsonResponse
    {
        $note = $request->input('note');
        $adminNote = is_string($note) ? $note : null;

        try {
            $result = $this->changeRequestAdminService->reject($id, $adminNote);
        } catch (InvalidArgumentException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 422);
        }

        if ($result === null) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan perubahan data tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan perubahan data berhasil ditolak.',
            'data' => $result,
        ]);
    }
}
