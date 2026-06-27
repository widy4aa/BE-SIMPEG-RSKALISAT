<?php

namespace App\Http\Controllers\Api\StrSip;

use App\Http\Controllers\Controller;
use App\Services\StrSip\StrSipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StrSipController extends Controller
{
    public function __construct(private readonly StrSipService $strSipService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $payload = $this->strSipService->getSummary($request->query());

        return response()->json([
            'success' => true,
            'message' => 'Data STR/SIP berhasil diambil.',
            'data' => $payload,
        ]);
    }
}
