<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove stored current flags; current status is computed from date ranges.
     */
    public function up(): void
    {
        foreach ($this->tables() as $table) {
            if (! Schema::hasColumn($table, 'is_current')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropColumn('is_current');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables() as $table) {
            if (Schema::hasColumn($table, 'is_current')) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->boolean('is_current')->default(false)->after($this->afterColumn($blueprint->getTable()));
            });
        }
    }

    /**
     * @return array<int, string>
     */
    private function tables(): array
    {
        return [
            'profesi_pegawai',
            'pangkat_pegawai',
            'jabatan_pegawai',
            'golongan_ruang_pegawai',
            'str',
            'sip',
            'penugasan_klinis',
        ];
    }

    private function afterColumn(string $table): string
    {
        return match ($table) {
            'profesi_pegawai' => 'profesi_id',
            'pangkat_pegawai' => 'pangkat_id',
            'jabatan_pegawai' => 'jabatan_id',
            'golongan_ruang_pegawai' => 'golongan_ruang_id',
            'str', 'sip' => 'tanggal_kadaluarsa',
            'penugasan_klinis' => 'tgl_kadaluarsa',
            default => 'id',
        };
    }
};
