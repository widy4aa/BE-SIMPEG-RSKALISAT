<?php

namespace Database\Seeders;

use App\Models\PerubahanData;
use App\Models\User;
use Illuminate\Database\Seeder;

class BudiProfileChangeRequestSeeder extends Seeder
{
    /**
     * Seed pengajuan perubahan profile untuk akun inti agar response profile lengkap.
     */
    public function run(): void
    {
        $requests = [
            [
                'username' => '3174010101010001',
                'status' => 'pending',
                'note' => 'Seeder: Pengajuan perubahan profile Budi',
                'changes' => [
                    ['target_table' => 'pegawai', 'kolom' => 'nama', 'value' => 'Budi Santoso, A.Md.Kep'],
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'alamat', 'value' => 'Jl. Kalisat Raya No. 123, Jember'],
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'no_telp', 'value' => '081355551234'],
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'email', 'value' => 'budi.santoso+update@rskalisat.test'],
                    ['target_table' => 'pegawai', 'kolom' => 'tmt_pns', 'value' => '2021-06-01'],
                ],
            ],
            [
                'username' => '3174010101010099',
                'status' => 'approved',
                'note' => 'Seeder: Riwayat perubahan profile Admin SIMPEG',
                'changes' => [
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'no_telp', 'value' => '081234560199'],
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'alamat', 'value' => 'Jl. Melati No. 10A, Kalisat'],
                ],
            ],
            [
                'username' => '3174010101010098',
                'status' => 'pending',
                'note' => 'Seeder: Pengajuan perubahan profile HRD SIMPEG',
                'changes' => [
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'email', 'value' => 'hrd.simpeg+update@rskalisat.test'],
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'alamat', 'value' => 'Jl. Kenanga No. 21B, Kalisat'],
                ],
            ],
            [
                'username' => '3174010101010003',
                'status' => 'approved',
                'note' => 'Seeder: Riwayat perubahan profile Direktur',
                'changes' => [
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'no_telp', 'value' => '081234560303'],
                    ['target_table' => 'pegawai_pribadi', 'kolom' => 'email', 'value' => 'agus.priyanto+update@rskalisat.test'],
                ],
            ],
        ];

        foreach ($requests as $seed) {
            $user = User::query()
                ->with('pegawai.pribadi')
                ->where('username', $seed['username'])
                ->first();

            if ($user === null || $user->pegawai === null) {
                continue;
            }

            $request = PerubahanData::query()->updateOrCreate(
                [
                    'by_user' => $user->id,
                    'fitur' => 'profile',
                    'note' => $seed['note'],
                ],
                [
                    'status' => $seed['status'],
                ]
            );

            $request->details()->delete();

            $details = collect($seed['changes'])
                ->map(fn (array $change): array => [
                    'target_table' => $change['target_table'],
                    'kolom' => $change['kolom'],
                    'old_value' => $this->resolveOldValue($user, $change['target_table'], $change['kolom']),
                    'value' => $change['value'],
                ])
                ->all();

            $request->details()->createMany($details);
        }
    }

    private function resolveOldValue(User $user, string $targetTable, string $column): ?string
    {
        $source = match ($targetTable) {
            'pegawai' => $user->pegawai,
            'pegawai_pribadi' => $user->pegawai?->pribadi,
            default => null,
        };

        $value = $source?->{$column} ?? null;

        return $value === null ? null : (string) $value;
    }
}
