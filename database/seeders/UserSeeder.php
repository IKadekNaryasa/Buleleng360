<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('users')->insertOrIgnore([
            [
                'id' => '30000000-0000-0000-0000-000000000001',
                'name' => 'Administrator Buleleng360',
                'email' => 'admin@buleleng360.test',
                'email_verified_at' => $now,
                'nip' => '200206092025061001',
                'password' => Hash::make('@PrakomKesbang01'),
                'role_id' => '10000000-0000-0000-0000-000000000001',
                'bidang_id' => '20000000-0000-0000-0000-000000000001',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
