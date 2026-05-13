<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StrSip\StrSipService;
use Illuminate\Http\JsonResponse;

class StrSipController extends Controller
{
    public function __construct(private readonly StrSipService $strSipService)
    {
    }

    public function index(): JsonResponse
    {
        $payload = $this->strSipService->getSummary();

        return response()->json([
            'success' => true,
            'message' => 'Data STR/SIP berhasil diambil.',
            'data' => $payload,
        ]);
    }
}
