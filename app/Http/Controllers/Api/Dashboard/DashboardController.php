<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    public function show(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);
        $type = $request->query('type');

        $payload = $this->dashboardService->getPayloadByRole($role, $userId, $type);

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
                'dashboard' => $payload['summary'],
            ],
        ]);
    }
}
