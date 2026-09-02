<?php

namespace Database\Seeders;

use App\Models\Agama;
use App\Models\Desa;
use App\Models\SebaranAgama;
use Illuminate\Database\Seeder;

class SebaranAgamaSeeder extends Seeder
{
    public function run(): void
    {
        $agamaMap = Agama::query()->pluck('id', 'agama');
        $data = [
            'Gerokgak' => [29243, 458, 69, 73753, 82, 0, 10],
            'Seririt' => [6291, 403, 63, 88278, 155, 1, 0],
            'Busungbiu' => [276, 136, 18, 52527, 8, 0, 0],
            'Banjar' => [3030, 440, 79, 84322, 400, 0, 0],
            'Sukasada' => [13894, 807, 233, 80512, 163, 0, 0],
            'Buleleng' => [21993, 2566, 1112, 126880, 3267, 78, 14],
            'Sawan' => [902, 496, 50, 84437, 66, 0, 0],
            'Kubutambahan' => [902, 158, 78, 73522, 68, 0, 1],
            'Tejakula' => [1660, 112, 48, 76798, 10, 0, 4],
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
        $agamaNames = ['islam', 'kristen_protestan', 'kristen_katolik', 'hindu', 'buddha', 'khonghucu', 'lainnya'];

        foreach ($data as $namaKecamatan => $jumlahAgama) {
            $desa = Desa::where('nama', $desaMap[$namaKecamatan])
                ->whereHas('kecamatan', fn($query) => $query->where('nama', $namaKecamatan))
                ->first();

            if (! $desa) {
                $this->command->warn("Desa {$namaKecamatan} tidak ditemukan.");

                continue;
            }

            foreach ($agamaNames as $index => $namaAgama) {
                SebaranAgama::updateOrCreate(
                    ['desa_id' => $desa->id, 'agama_id' => $agamaMap[$namaAgama]],
                    ['jumlah_pemeluk' => $jumlahAgama[$index]],
                );
            }
        }
    }
}
