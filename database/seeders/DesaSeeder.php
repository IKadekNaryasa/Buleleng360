<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('desas')->insertOrIgnore([
            ['id' => '50000000-0000-0000-0000-000000000001', 'kecamatan_id' => '40000000-0000-0000-0000-000000000001', 'nama' => 'Kampung Kajanan', 'lat' => -8.1100000, 'long' => 115.0900000, 'geojson_boundary' => json_encode([]), 'created_at' => $now, 'updated_at' => $now],
            ['id' => '50000000-0000-0000-0000-000000000002', 'kecamatan_id' => '40000000-0000-0000-0000-000000000001', 'nama' => 'Kampung Anyar', 'lat' => -8.1080000, 'long' => 115.0860000, 'geojson_boundary' => json_encode([]), 'created_at' => $now, 'updated_at' => $now],
            ['id' => '50000000-0000-0000-0000-000000000003', 'kecamatan_id' => '40000000-0000-0000-0000-000000000002', 'nama' => 'Banyuasri', 'lat' => -8.1170000, 'long' => 115.0970000, 'geojson_boundary' => json_encode([]), 'created_at' => $now, 'updated_at' => $now],
            ['id' => '50000000-0000-0000-0000-000000000004', 'kecamatan_id' => '40000000-0000-0000-0000-000000000002', 'nama' => 'Astina', 'lat' => -8.1200000, 'long' => 115.0920000, 'geojson_boundary' => json_encode([]), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
