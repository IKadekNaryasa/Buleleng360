<?php

namespace Database\Seeders;

use App\Models\Desa;
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
        $desa = Desa::orderBy('nama')->take(4)->get();

        if ($desa->count() < 4) {
            $this->command->warn('Data penduduk dilewati karena minimal 4 desa diperlukan.');

            return;
        }

        DB::table('penduduks')->insertOrIgnore([
            ['id' => '90000000-0000-0000-0000-000000000001', 'desa_id' => $desa[0]->id, 'total_jiwa' => 21000, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
            ['id' => '90000000-0000-0000-0000-000000000002', 'desa_id' => $desa[1]->id, 'total_jiwa' => 18500, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
            ['id' => '90000000-0000-0000-0000-000000000003', 'desa_id' => $desa[2]->id, 'total_jiwa' => 24000, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
            ['id' => '90000000-0000-0000-0000-000000000004', 'desa_id' => $desa[3]->id, 'total_jiwa' => 22000, 'tahun' => 2025, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
