<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class DesaSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/desa_data.json');
        $data = json_decode(file_get_contents($path), true);
        $kecamatanMap = Kecamatan::all()->keyBy('nama');

        $skipped = [];

        foreach ($data as $item) {
            $kecamatan = $kecamatanMap->get($item['nama_kecamatan']);

            if (!$kecamatan) {
                $skipped[] = $item['nama_desa'] . ' (' . $item['nama_kecamatan'] . ')';
                continue;
            }

            Desa::create([
                'nama' => $item['nama_desa'],
                'lat' => $item['lat'],
                'long' => $item['long'],
                'kecamatan_id' => $kecamatan->id,
                'geojson_boundary' => $item['geojson_boundary'],
            ]);
        }

        if (!empty($skipped)) {
            $this->command->warn('Desa dilewati karena kecamatan tidak ditemukan: ' . implode(', ', $skipped));
        }

        $this->command->info('Berhasil impor ' . (count($data) - count($skipped)) . ' desa.');
    }
}
