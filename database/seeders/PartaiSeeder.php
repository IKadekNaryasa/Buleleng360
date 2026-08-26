<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Partai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/partai_data.json');
        $data = json_decode(file_get_contents($path), true);
        $desaMap = Desa::all()->keyBy('nama');

        $skipped = [];

        foreach ($data as $item) {
            $desa = $desaMap->get($item['nama_desa']);

            if (!$desa) {
                $skipped[] = $item['nama_partai'] . ' (' . $item['nama_desa'] . ')';
                continue;
            }

            Partai::updateOrCreate(
                ['desa_id' => $desa->id, 'nama' => $item['nama_partai']],
                [
                    'jumlah_kader' => $item['jumlah_kader'],
                    'ketua' => $item['ketua'],
                    'sekretaris' => $item['sekretaris'],
                    'bendahara' => $item['bendahara'],
                    'alamat' => $item['alamat'],
                    'lat' => $item['lat'],
                    'long' => $item['long'],
                ]
            );
        }

        if (!empty($skipped)) {
            echo "Skipped Partai:\n";
            foreach ($skipped as $item) {
                echo "- $item\n";
            }
        }
    }
}
