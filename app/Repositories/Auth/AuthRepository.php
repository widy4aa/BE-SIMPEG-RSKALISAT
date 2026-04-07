<?php

namespace App\Repositories\Auth;

use App\Models\User;

class AuthRepository
{
    public function findByNik(string $nik): ?User
    {
        return User::query()
            ->where('username', $nik)
            ->with('pegawai')
            ->first();
    }
}
