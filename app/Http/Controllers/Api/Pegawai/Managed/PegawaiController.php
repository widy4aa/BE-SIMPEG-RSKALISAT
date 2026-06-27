<?php

namespace App\Http\Controllers\Api\Pegawai\Managed;

use App\Http\Controllers\Controller;
use App\Http\Requests\Hrd\UpdatePegawaiIntiRequest;
use App\Http\Requests\Hrd\UpdatePegawaiPribadiRequest;
use App\Services\Pegawai\Managed\PegawaiUpdateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class PegawaiController extends Controller
{
    public function __construct(private readonly PegawaiUpdateService $service) {}

    public function updateInti(UpdatePegawaiIntiRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->updateInti($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Data inti pegawai berhasil diperbarui.',
                'data'    => $result,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server: '.$e->getMessage()], 500);
        }
    }

    public function updatePribadi(UpdatePegawaiPribadiRequest $request, int $id): JsonResponse
    {
        try {
            $result = $this->service->updatePribadi(
                $id,
                $request->validated(),
                $request->file('foto_profil'),
                $request->file('ktp_file'),
                $request->file('kk_file')
            );

            return response()->json([
                'success' => true,
                'message' => 'Data pribadi pegawai berhasil diperbarui.',
                'data'    => $result,
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server: '.$e->getMessage()], 500);
        }
    }
}
