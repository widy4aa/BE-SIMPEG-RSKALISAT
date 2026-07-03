<?php

namespace App\Services\Hrd;

use App\Models\Pegawai;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    private const DEFAULT_SETTINGS = [
        'promosi_min_masa_kerja' => 2,
        'promosi_min_jp_diklat' => 40,
        'promosi_wajib_str_aktif' => true,
        'promosi_bobot_masa_kerja' => 30,
        'promosi_bobot_diklat' => 40,
        'promosi_bobot_pendidikan' => 30,
        'promosi_passing_grade' => 75,
    ];

    public function getSettings(): array
    {
        $keys = array_keys(self::DEFAULT_SETTINGS);
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        $result = [];
        foreach (self::DEFAULT_SETTINGS as $key => $default) {
            $value = array_key_exists($key, $settings) ? $settings[$key] : $default;
            
            // Cast based on default value type
            if (is_bool($default)) {
                $result[$key] = in_array(strtolower((string) $value), ['1', 'true', 'yes'], true);
            } elseif (is_int($default) || is_float($default)) {
                $result[$key] = (float) $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function updateSettings(array $data): array
    {
        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                // Ensure booleans are stored as '1' or '0'
                if (is_bool($value)) {
                    $value = $value ? '1' : '0';
                }
                
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => (string) $value]
                );
            }
        });

        return $this->getSettings();
    }

    public function getRecommendations(): array
    {
        $settings = $this->getSettings();
        
        $pegawaiList = Pegawai::with([
            'jabatanPegawai.jabatan', 
            'jadwalDiklat.diklat',
            'str',
            'pribadi.pendidikan'
        ])->where('status_pegawai', 'aktif')->get();

        $recommendations = [];

        foreach ($pegawaiList as $pegawai) {
            // Cek syarat mutlak STR jika tenkes
            if ($settings['promosi_wajib_str_aktif'] && $this->isTenagaKesehatan($pegawai)) {
                if (!$this->hasActiveStr($pegawai)) {
                    continue; // Gugur karena STR tidak aktif
                }
            }

            $skorMasaKerja = 0.0;
            $skorDiklat = 0.0;
            $skorPendidikan = 0.0;
            $alasan = [];

            // 1. Kalkulasi Masa Kerja
            $masaKerja = $this->calculateMasaKerja($pegawai);
            if ($masaKerja >= $settings['promosi_min_masa_kerja']) {
                $skorMasaKerja = min($settings['promosi_bobot_masa_kerja'], ($masaKerja / max(1, $settings['promosi_min_masa_kerja'])) * $settings['promosi_bobot_masa_kerja']);
                $alasan[] = "Masa kerja $masaKerja tahun (memenuhi kriteria minimal).";
            }

            // 2. Kalkulasi Diklat
            $totalJp = $this->calculateTotalJp($pegawai);
            if ($totalJp >= $settings['promosi_min_jp_diklat']) {
                $skorDiklat = min($settings['promosi_bobot_diklat'], ($totalJp / max(1, $settings['promosi_min_jp_diklat'])) * $settings['promosi_bobot_diklat']);
                $alasan[] = "Memiliki $totalJp JP Diklat Valid.";
            }

            // 3. Kalkulasi Pendidikan (Asumsi sederhana: punya pendidikan terakhir = skor max, atau bisa dibuat lebih kompleks nanti)
            if ($pegawai->pribadi && $pegawai->pribadi->pendidikan->isNotEmpty()) {
                $skorPendidikan = $settings['promosi_bobot_pendidikan'];
                $alasan[] = "Riwayat pendidikan tercatat.";
            }

            $skorTotal = $skorMasaKerja + $skorDiklat + $skorPendidikan;

            if ($skorTotal >= $settings['promosi_passing_grade']) {
                $recommendations[] = [
                    'pegawai_id' => $pegawai->id,
                    'nik' => $pegawai->nik,
                    'nama' => $pegawai->nama,
                    'jabatan_saat_ini' => $pegawai->jabatan?->nama ?? '-',
                    'skor_total' => round($skorTotal, 2),
                    'breakdown_skor' => [
                        'masa_kerja' => round($skorMasaKerja, 2),
                        'diklat' => round($skorDiklat, 2),
                        'pendidikan' => round($skorPendidikan, 2),
                    ],
                    'keterangan' => implode(' ', $alasan),
                ];
            }
        }

        // Urutkan berdasarkan skor total tertinggi
        usort($recommendations, fn($a, $b) => $b['skor_total'] <=> $a['skor_total']);

        return $recommendations;
    }

    private function isTenagaKesehatan(Pegawai $pegawai): bool
    {
        // Simple heuristic: If they have STR records or profesi related to tenkes
        return $pegawai->str()->exists();
    }

    private function hasActiveStr(Pegawai $pegawai): bool
    {
        $activeStr = $pegawai->str->filter(function ($str) {
            if (!$str->tanggal_kadaluarsa) {
                return false;
            }
            return Carbon::parse($str->tanggal_kadaluarsa)->isFuture();
        });

        return $activeStr->isNotEmpty();
    }

    private function calculateMasaKerja(Pegawai $pegawai): float
    {
        if (!$pegawai->tgl_masuk) {
            return 0.0;
        }

        $masuk = Carbon::parse($pegawai->tgl_masuk);
        $sekarang = Carbon::now();

        return round($masuk->floatDiffInYears($sekarang), 1);
    }

    private function calculateTotalJp(Pegawai $pegawai): int
    {
        $totalJp = 0;
        foreach ($pegawai->jadwalDiklat as $jadwal) {
            // Hanya hitung jika layak dan valid (opsional: bisa disesuaikan dengan kebutuhan)
            if (strtolower((string) $jadwal->status_kelayakan) === 'layak' && strtolower((string) $jadwal->status_validasi) === 'valid') {
                $totalJp += (int) ($jadwal->diklat->jp ?? 0);
            }
        }
        return $totalJp;
    }
}
