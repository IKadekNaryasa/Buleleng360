<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('bidangs')->insertOrIgnore([
            ['id' => '20000000-0000-0000-0000-000000000001', 'name' => 'Kependudukan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => '20000000-0000-0000-0000-000000000002', 'name' => 'Sosial Politik', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
