<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SebaranAgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $jumlahPemeluk = [
            '80000000-0000-0000-0000-000000000001' => 120,
            '80000000-0000-0000-0000-000000000002' => 20,
            '80000000-0000-0000-0000-000000000003' => 15,
            '80000000-0000-0000-0000-000000000004' => 250,
            '80000000-0000-0000-0000-000000000005' => 8,
            '80000000-0000-0000-0000-000000000006' => 2,
            '80000000-0000-0000-0000-000000000007' => 5,
        ];

        foreach (['50000000-0000-0000-0000-000000000001', '50000000-0000-0000-0000-000000000002', '50000000-0000-0000-0000-000000000003', '50000000-0000-0000-0000-000000000004'] as $desaId) {
            foreach ($jumlahPemeluk as $agamaId => $jumlah) {
                DB::table('sebaran_agamas')->updateOrInsert(
                    ['agama_id' => $agamaId, 'desa_id' => $desaId],
                    ['id' => (string) Str::uuid(), 'jumlah_pemeluk' => $jumlah, 'created_at' => $now, 'updated_at' => $now],
                );
            }
        }
    }
}
