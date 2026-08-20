<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('agamas')->insertOrIgnore([
            ['id' => '80000000-0000-0000-0000-000000000001', 'agama' => 'islam'],
            ['id' => '80000000-0000-0000-0000-000000000002', 'agama' => 'kristen_protestan'],
            ['id' => '80000000-0000-0000-0000-000000000003', 'agama' => 'kristen_katolik'],
            ['id' => '80000000-0000-0000-0000-000000000004', 'agama' => 'hindu'],
            ['id' => '80000000-0000-0000-0000-000000000005', 'agama' => 'buddha'],
            ['id' => '80000000-0000-0000-0000-000000000006', 'agama' => 'khonghucu'],
            ['id' => '80000000-0000-0000-0000-000000000007', 'agama' => 'lainnya'],
        ]);
    }
}
