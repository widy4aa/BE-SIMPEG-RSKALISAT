<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use App\Services\Security\JwtService;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly AuthRepository $authRepository,
        private readonly JwtService $jwtService,
    ) {
    }

    public function login(string $nik, string $password): array
    {
        $user = $this->authRepository->findByNik($nik);

        if (! $user || ! Hash::check($password, $user->password)) {
            return [
                'success' => false,
                'message' => 'NIK atau password tidak valid.',
                'status' => 401,
            ];
        }

        if (! $user->is_active) {
            return [
                'success' => false,
                'message' => 'Akun tidak aktif. Silakan hubungi admin.',
                'status' => 403,
            ];
        }

        $jwt = $this->jwtService->generate([
            'sub' => $user->id,
            'nik' => $user->username,
            'role' => $user->role,
        ]);

        return [
            'success' => true,
            'message' => 'Login berhasil.',
            'status' => 200,
            'data' => [
                'token_type' => 'Bearer',
                'access_token' => $jwt['token'],
                'expires_in' => $jwt['expires_in'],
                'user' => [
                    'id' => $user->id,
                    'nik' => $user->username,
                    'role' => $user->role,
                    'nama' => optional($user->pegawai)->nama,
                ],
            ],
        ];
    }
}
