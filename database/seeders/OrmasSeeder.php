<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        DB::table('ormas')->insertOrIgnore([
            ['id' => '70000000-0000-0000-0000-000000000001', 'desa_id' => '50000000-0000-0000-0000-000000000002', 'nama' => 'Pemuda Kampung Anyar', 'jumlah_anggota' => 75, 'ketua' => 'I Made Arya', 'sekretaris' => 'Ni Putu Dewi', 'bendahara' => 'I Komang Budi', 'lat' => -8.1080000, 'long' => 115.0860000, 'alamat' => 'Kampung Anyar', 'created_at' => $now, 'updated_at' => $now],
            ['id' => '70000000-0000-0000-0000-000000000002', 'desa_id' => '50000000-0000-0000-0000-000000000004', 'nama' => 'Forum Warga Astina', 'jumlah_anggota' => 55, 'ketua' => 'I Ketut Rai', 'sekretaris' => 'Ni Wayan Ayu', 'bendahara' => 'I Gede Suta', 'lat' => -8.1200000, 'long' => 115.0920000, 'alamat' => 'Astina', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
