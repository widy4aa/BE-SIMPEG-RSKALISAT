<?php

namespace App\Http\Middleware;

use App\Services\Security\JwtService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    public function __construct(private readonly JwtService $jwtService)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $authorization = (string) $request->header('Authorization', '');

        if (! str_starts_with($authorization, 'Bearer ')) {
            return $this->accessDenied(401);
        }

        $token = trim(substr($authorization, 7));

        if ($token === '') {
            return $this->accessDenied(401);
        }

        $claims = $this->jwtService->verify($token);

        if ($claims === null) {
            return $this->accessDenied(401);
        }

        $request->merge([
            '_jwt_claims' => $claims,
        ]);

        return $next($request);
    }

    private function accessDenied(int $status): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Access denied.',
        ], $status);
    }
}
