<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Ormas;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/ormas_data.json');
        $data = json_decode(file_get_contents($path), true);
        $desaMap = Desa::all()->keyBy('nama');

        $skipped = [];

        foreach ($data as $item) {
            $desa = $desaMap->get($item['nama_desa']);

            if (!$desa) {
                $skipped[] = $item['nama_ormas'] . ' (' . $item['nama_desa'] . ')';
                continue;
            }

            Ormas::updateOrCreate(
                ['desa_id' => $desa->id, 'nama' => $item['nama_ormas']],
                [
                    'jumlah_anggota' => $item['jumlah_anggota'],
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
            echo "Skipped Ormas:\n";
            foreach ($skipped as $item) {
                echo "- $item\n";
            }
        }
    }
}
