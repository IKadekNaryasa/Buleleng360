<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('kecamatans')->insertOrIgnore([
            ['id' => '40000000-0000-0000-0000-000000000001', 'nama' => 'Buleleng', 'lat' => -8.1120000, 'long' => 115.0880000, 'geojson_boundary' => json_encode([]), 'created_at' => $now, 'updated_at' => $now],
            ['id' => '40000000-0000-0000-0000-000000000002', 'nama' => 'Singaraja', 'lat' => -8.1150000, 'long' => 115.0950000, 'geojson_boundary' => json_encode([]), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
