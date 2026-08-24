<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Penduduk;
use Illuminate\Database\Seeder;

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

        $jumlahPenduduk = [21000, 18500, 24000, 22000];

        foreach (Desa::orderBy('nama')->get() as $index => $desaItem) {
            Penduduk::updateOrCreate(
                ['desa_id' => $desaItem->id, 'tahun' => 2025],
                ['total_jiwa' => $jumlahPenduduk[$index] ?? 420],
            );
        }
    }
}
