<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('partais')->insertOrIgnore([
            ['id' => '60000000-0000-0000-0000-000000000001', 'desa_id' => '50000000-0000-0000-0000-000000000001', 'nama' => 'Partai Demokrasi Buleleng', 'jumlah_kader' => 125, 'ketua' => 'I Made Putra', 'sekretaris' => 'Ni Luh Sari', 'bendahara' => 'I Ketut Dana', 'lat' => -8.1100000, 'long' => 115.0900000, 'alamat' => 'Kampung Kajanan', 'created_at' => $now, 'updated_at' => $now],
            ['id' => '60000000-0000-0000-0000-000000000002', 'desa_id' => '50000000-0000-0000-0000-000000000003', 'nama' => 'Partai Rakyat Singaraja', 'jumlah_kader' => 90, 'ketua' => 'I Wayan Adi', 'sekretaris' => 'Ni Made Rina', 'bendahara' => 'I Nyoman Jaya', 'lat' => -8.1170000, 'long' => 115.0970000, 'alamat' => 'Banyuasri', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
