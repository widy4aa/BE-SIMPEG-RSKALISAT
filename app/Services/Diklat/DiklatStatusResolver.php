<?php

namespace App\Services\Diklat;

use Carbon\Carbon;

class DiklatStatusResolver
{
    public function displayStatus(mixed $tanggalMulai, mixed $tanggalSelesai): string
    {
        $today = Carbon::today();
        $mulai = $this->normalizeDate($tanggalMulai);
        $selesai = $this->normalizeDate($tanggalSelesai);

        if ($mulai !== null && $today->lt($mulai)) {
            return 'mendatang';
        }

        if ($selesai !== null && $today->gt($selesai)) {
            return 'selesai';
        }

        return 'berlangsung';
    }

    public function jadwalStatus(mixed $tanggalMulai, mixed $tanggalSelesai): string
    {
        $today = Carbon::today();
        $mulai = $this->normalizeDate($tanggalMulai);
        $selesai = $this->normalizeDate($tanggalSelesai);

        if ($mulai !== null && $today->lt($mulai)) {
            return 'belum terlaksana';
        }

        if ($selesai !== null && $today->gt($selesai)) {
            return 'sudah terlaksana';
        }

        return 'sedang terlaksana';
    }

    private function normalizeDate(mixed $date): ?Carbon
    {
        if ($date instanceof Carbon) {
            return $date->copy()->startOfDay();
        }

        return $date ? Carbon::parse($date)->startOfDay() : null;
    }
}
