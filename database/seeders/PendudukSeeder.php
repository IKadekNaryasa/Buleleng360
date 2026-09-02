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
        $data = [
            'Gerokgak' => 103615,
            'Seririt' => 95191,
            'Busungbiu' => 52965,
            'Banjar' => 88271,
            'Sukasada' => 95609,
            'Buleleng' => 155910,
            'Sawan' => 85951,
            'Kubutambahan' => 74729,
            'Tejakula' => 78632,
        ];
        $desaMap = [
            'Gerokgak' => 'Gerokgak',
            'Seririt' => 'Kelurahan Seririt',
            'Busungbiu' => 'Busungbiu',
            'Banjar' => 'Banjar',
            'Sukasada' => 'Kelurahan Sukasada',
            'Buleleng' => 'Alasangker',
            'Sawan' => 'Sawan',
            'Kubutambahan' => 'Bengkala',
            'Tejakula' => 'Tejakula',
        ];

        foreach ($data as $namaKecamatan => $totalJiwa) {
            $desa = Desa::where('nama', $desaMap[$namaKecamatan])
                ->whereHas('kecamatan', fn($query) => $query->where('nama', $namaKecamatan))
                ->first();

            if (! $desa) {
                $this->command->warn("Desa {$namaKecamatan} tidak ditemukan.");

                continue;
            }

            Penduduk::updateOrCreate(
                ['desa_id' => $desa->id, 'tahun' => 2025],
                ['total_jiwa' => $totalJiwa],
            );
        }
    }
}
