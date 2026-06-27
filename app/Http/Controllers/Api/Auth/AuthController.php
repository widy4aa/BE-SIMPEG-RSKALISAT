<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangeNikRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Pegawai;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            nik: (string) $request->string('nik'),
            password: (string) $request->string('password')
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], $result['status']);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data'],
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        // JWT bersifat stateless — tidak ada server-side blacklist.
        // Client bertanggung jawab menghapus token setelah endpoint ini dipanggil.
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil. Silakan hapus token di sisi client.',
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);

        $user = User::find($userId);

        if ($user === null) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        if (! Hash::check($request->input('password_lama'), $user->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 422);
        }

        $user->update(['password' => Hash::make($request->input('password_baru'))]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }

    public function changeNik(ChangeNikRequest $request): JsonResponse
    {
        $claims = $request->input('_jwt_claims', []);
        $userId = (int) (is_array($claims) ? ($claims['sub'] ?? 0) : 0);
        $nikBaru = $request->input('nik');

        $user = User::find($userId);
        if ($user === null) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        $pegawai = Pegawai::where('user_id', $userId)->first();
        if ($pegawai === null) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $duplicate = DB::selectOne(
            'SELECT id FROM pegawai WHERE nik = ? AND id != ? AND deleted_at IS NULL LIMIT 1',
            [$nikBaru, $pegawai->id]
        );
        if ($duplicate) {
            return response()->json(['success' => false, 'message' => 'NIK sudah digunakan.'], 422);
        }

        DB::transaction(function () use ($user, $pegawai, $nikBaru): void {
            $user->update(['username' => $nikBaru]);
            $pegawai->update(['nik' => $nikBaru]);
        });

        return response()->json([
            'success' => true,
            'message' => 'NIK berhasil diubah.',
            'data' => ['nik' => $nikBaru],
        ]);
    }
}
