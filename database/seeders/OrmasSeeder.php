<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Ormas;
use Illuminate\Database\Seeder;

class OrmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/ormas_data.json');
        $data = json_decode(file_get_contents($path), true);

        // Eager load relasi kecamatan supaya tidak N+1 query
        $desaMap = Desa::with('kecamatan')->get()
            ->keyBy(function ($desa) {
                return $this->normalizeKey($desa->nama, $desa->kecamatan->nama);
            });

        $skipped = [];

        foreach ($data as $item) {
            $key = $this->normalizeKey($item['nama_desa'], $item['nama_kecamatan']);
            $desa = $desaMap->get($key);

            if (!$desa) {
                $skipped[] = $item['nama_ormas'] . ' (' . $item['nama_desa'] . ' - ' . $item['nama_kecamatan'] . ')';
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


    private function normalizeKey(string $namaDesa, string $namaKecamatan): string
    {
        $desa = strtolower(trim(preg_replace('/\s+/', ' ', $namaDesa)));
        $kecamatan = strtolower(trim(preg_replace('/\s+/', ' ', $namaKecamatan)));

        return $desa . '|' . $kecamatan;
    }
}
