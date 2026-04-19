<?php

namespace Database\Seeders;

use App\Models\PerubahanData;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudiProfileChangeRequestSeeder extends Seeder
{
    /**
     * Seed 1 pengajuan perubahan profile milik Budi untuk ditinjau admin.
     */
    public function run(): void
    {
        $budi = User::query()
            ->with('pegawai.pribadi')
            ->where('username', '3174010101010001')
            ->first();

        if ($budi === null || $budi->pegawai === null) {
            return;
        }

        $pegawai = $budi->pegawai;
        $pribadi = $pegawai->pribadi;

        $request = PerubahanData::query()->firstOrCreate(
            [
                'by_user' => $budi->id,
                'fitur' => 'profile',
                'status' => 'pending',
                'note' => 'Seeder: Pengajuan perubahan profile Budi',
            ],
            [
                'by_user' => $budi->id,
                'fitur' => 'profile',
                'status' => 'pending',
                'note' => 'Seeder: Pengajuan perubahan profile Budi',
            ]
        );

        // Reset detail agar hasil seeding konsisten saat dijalankan ulang.
        $request->details()->delete();

        $request->details()->createMany([
            [
                'target_table' => 'pegawai',
                'kolom' => 'nama',
                'old_value' => (string) ($pegawai->nama ?? ''),
                'value' => 'Budi Santoso, S.Kom',
            ],
            [
                'target_table' => 'pegawai_pribadi',
                'kolom' => 'alamat',
                'old_value' => (string) ($pribadi->alamat ?? ''),
                'value' => 'Jl. Kalisat Raya No. 123, Jember',
            ],
            [
                'target_table' => 'pegawai_pribadi',
                'kolom' => 'no_telp',
                'old_value' => (string) ($pribadi->no_telp ?? ''),
                'value' => '081355551234',
            ],
            [
                'target_table' => 'pegawai_pribadi',
                'kolom' => 'email',
                'old_value' => (string) ($pribadi->email ?? ''),
                'value' => 'budi.santoso+update@example.com',
            ],
            [
                'target_table' => 'pegawai',
                'kolom' => 'tmt_pns',
                'old_value' => optional($pegawai->tmt_pns)?->toDateString(),
                'value' => '2021-06-01',
            ],
        ]);
    }
}
