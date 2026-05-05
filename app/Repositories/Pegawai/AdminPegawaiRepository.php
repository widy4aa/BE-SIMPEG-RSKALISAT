<?php

namespace App\Repositories\Pegawai;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\PegawaiPribadi;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPegawaiRepository
{
    public function getAllPegawai(): Collection
    {
        return Pegawai::with([
            'user',
            'pribadi',
            'profesi',
            'jabatan.unitKerja'
        ])->get();
    }

    public function createPegawaiData(array $data): Pegawai
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['nik'],
                'password' => Hash::make($data['password']),
                'role' => 'pegawai',
                'is_active' => true,
            ]);

            $pegawai = Pegawai::create([
                'user_id' => $user->id,
                'nik' => $data['nik'],
                'nama' => $data['nama'],
            ]);

            PegawaiPribadi::create([
                'pegawai_id' => $pegawai->id,
            ]);

            return $pegawai;
        });
    }

    public function changeRole(int $pegawaiId, string $newRole): Pegawai
    {
        $pegawai = Pegawai::with('user')->findOrFail($pegawaiId);
        
        if ($pegawai->user) {
            $pegawai->user->role = $newRole;
            $pegawai->user->save();
        }

        return $pegawai;
    }
}
