<?php

namespace App\Http\Controllers\Api\Hrd;

use App\Http\Controllers\Controller;
use App\Services\Hrd\PromotionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PromotionController extends Controller
{
    public function __construct(
        private readonly PromotionService $promotionService
    ) {
    }

    public function getSettings(): JsonResponse
    {
        $settings = $this->promotionService->getSettings();

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan rekomendasi promosi berhasil diambil.',
            'data' => $settings,
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'promosi_min_masa_kerja' => 'required|numeric|min:0',
                'promosi_min_jp_diklat' => 'required|numeric|min:0',
                'promosi_wajib_str_aktif' => 'required|boolean',
                'promosi_bobot_masa_kerja' => 'required|numeric|min:0|max:100',
                'promosi_bobot_diklat' => 'required|numeric|min:0|max:100',
                'promosi_bobot_pendidikan' => 'required|numeric|min:0|max:100',
                'promosi_passing_grade' => 'required|numeric|min:0|max:100',
            ]);

            $settings = $this->promotionService->updateSettings($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan rekomendasi promosi berhasil diperbarui.',
                'data' => $settings,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function getRecommendations(): JsonResponse
    {
        $recommendations = $this->promotionService->getRecommendations();

        return response()->json([
            'success' => true,
            'message' => 'Data rekomendasi promosi berhasil dihitung berdasarkan konfigurasi terbaru.',
            'data' => $recommendations,
        ]);
    }
}
