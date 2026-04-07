<?php

namespace Database\Seeders;

use App\Models\JenisDiklat;
use App\Models\JenisPegawai;
use App\Models\JenisSip;
use App\Models\KategoriDiklat;
use App\Models\Profesi;
use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class MasterReferensiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (['Administrasi', 'SDM', 'Pelayanan Medis', 'Keuangan'] as $nama) {
            UnitKerja::query()->firstOrCreate(['nama' => $nama]);
        }

        foreach (['PNS', 'PPPK Penuh Waktu', 'PPPK Paruh Waktu', 'BLUD'] as $nama) {
            JenisPegawai::query()->firstOrCreate(['nama' => $nama]);
        }

        $profesi = [
            ['nama' => 'Dokter', 'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Bidan', 'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Perawat', 'kategori_tenaga' => 'Tenaga Kesehatan'],
            ['nama' => 'Analis SDM', 'kategori_tenaga' => 'Non Kesehatan'],
        ];

        foreach ($profesi as $item) {
            Profesi::query()->firstOrCreate(
                ['nama' => $item['nama']],
                ['kategori_tenaga' => $item['kategori_tenaga']]
            );
        }

        foreach (['SIP Praktik Mandiri', 'SIP Praktik Bersama', 'SIP Praktik Rumah Sakit'] as $nama) {
            JenisSip::query()->firstOrCreate(['nama' => $nama]);
        }

        foreach (['ASN', 'Tenkes'] as $nama) {
            JenisDiklat::query()->firstOrCreate(['nama' => $nama]);
        }

        foreach (['Struktural', 'Fungsional', 'Teknis', 'Akred'] as $nama) {
            KategoriDiklat::query()->firstOrCreate(['nama' => $nama]);
        }
    }
}
