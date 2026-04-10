<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        $claims = $request->input('_jwt_claims');

        if (! is_array($claims)) {
            return $this->accessDenied();
        }

        $role = (string) ($claims['role'] ?? '');

        if ($role === '' || ! in_array($role, $allowedRoles, true)) {
            return $this->accessDenied();
        }

        return $next($request);
    }

    private function accessDenied(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Access denied.',
        ], 403);
    }
}
