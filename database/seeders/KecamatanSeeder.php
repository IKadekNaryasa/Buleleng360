<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/kecamatan_data.json');
        $data = json_decode(file_get_contents($path), true);

        foreach ($data as $item) {
            Kecamatan::create([
                'nama' => $item['nama_kecamatan'],
                'lat' => $item['lat'],
                'long' => $item['long'],
            ]);
        }
    }
}
