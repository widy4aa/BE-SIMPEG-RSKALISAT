<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $role = (string) (is_array($claims) ? ($claims['role'] ?? '') : '');

        $message = match ($role) {
            'admin' => 'Selamat datang admin.',
            'pegawai' => 'Selamat datang pegawai.',
            'hrd' => 'Selamat datang hrd.',
            'direktur' => 'Selamat datang direktur.',
            default => 'Access denied.',
        };

        if ($message === 'Access denied.') {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'role' => $role,
            ],
        ]);
    }
}
