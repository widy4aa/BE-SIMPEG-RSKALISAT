<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MasterReferensiSeeder::class,
            PegawaiSeeder::class,
            PegawaiDummySeeder::class,
            RiwayatPegawaiSeeder::class,
            DiklatPegawaiBudiSeeder::class,
            PegawaiNotificationSeeder::class,
            PegawaiActionNotificationSeeder::class,
            BudiProfileChangeRequestSeeder::class,
        ]);
    }
}
