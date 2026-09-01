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
        $failed = [];

        foreach ($data as $item) {
            $kecamatan = $kecamatanMap->get($item['nama_kecamatan']);

            if (!$kecamatan) {
                $skipped[] = $item['nama_desa'] . ' (' . $item['nama_kecamatan'] . ')';
                continue;
            }

            try {
                Desa::updateOrCreate(
                    [
                        'nama' => $item['nama_desa'],
                        'kecamatan_id' => $kecamatan->id, // penting: composite key, bukan nama saja
                    ],
                    [
                        'lat' => $item['lat'],
                        'long' => $item['long'],
                        'geojson_boundary' => $item['geojson_boundary'],
                    ]
                );
            } catch (\Throwable $e) {
                // supaya 1 baris gagal TIDAK menghentikan seluruh proses
                $failed[] = $item['nama_desa'] . ': ' . $e->getMessage();
            }
        }

        if (!empty($skipped)) {
            $this->command->warn('Desa dilewati karena kecamatan tidak ditemukan: ' . implode(', ', $skipped));
        }
        if (!empty($failed)) {
            $this->command->error('Desa gagal disimpan: ' . implode(' | ', $failed));
        }

        $this->command->info('Berhasil impor ' . (count($data) - count($skipped) - count($failed)) . ' desa.');
    }
}
