<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('roles')->insertOrIgnore([
            ['id' => '10000000-0000-0000-0000-000000000001', 'name' => 'Administrator', 'created_at' => $now, 'updated_at' => $now],
            ['id' => '10000000-0000-0000-0000-000000000002', 'name' => 'Operator', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
