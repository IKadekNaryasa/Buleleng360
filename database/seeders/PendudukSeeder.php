<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendudukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('penduduks')->insertOrIgnore([
            ['id' => '90000000-0000-0000-0000-000000000001', 'desa_id' => '50000000-0000-0000-0000-000000000001', 'total_jiwa' => 21000, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
            ['id' => '90000000-0000-0000-0000-000000000002', 'desa_id' => '50000000-0000-0000-0000-000000000002', 'total_jiwa' => 18500, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
            ['id' => '90000000-0000-0000-0000-000000000003', 'desa_id' => '50000000-0000-0000-0000-000000000003', 'total_jiwa' => 24000, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
            ['id' => '90000000-0000-0000-0000-000000000004', 'desa_id' => '50000000-0000-0000-0000-000000000004', 'total_jiwa' => 22000, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
