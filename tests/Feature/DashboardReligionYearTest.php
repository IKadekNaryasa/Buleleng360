<?php

use App\Models\Agama;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Penduduk;
use App\Models\SebaranAgama;

it('uses the sebaran agama year for dashboard data and population totals', function () {
    $kecamatan = Kecamatan::create([
        'nama' => 'Sukasada',
        'lat' => -8.1,
        'long' => 115.1,
    ]);
    $desa = Desa::create([
        'kecamatan_id' => $kecamatan->id,
        'nama' => 'Pegadungan',
        'lat' => -8.2,
        'long' => 115.15,
        'geojson_boundary' => [
            'type' => 'Polygon',
            'coordinates' => [[[115.1, -8.1], [115.2, -8.1], [115.2, -8.2], [115.1, -8.2], [115.1, -8.1]]],
        ],
    ]);
    $agama = Agama::create(['agama' => 'islam']);

    SebaranAgama::create([
        'desa_id' => $desa->id,
        'agama_id' => $agama->id,
        'jumlah_pemeluk' => 50,
        'tahun' => 2024,
    ]);
    Penduduk::create([
        'desa_id' => $desa->id,
        'total_jiwa' => 100,
        'tahun' => 2024,
    ]);
    Penduduk::create([
        'desa_id' => $desa->id,
        'total_jiwa' => 200,
        'tahun' => 2025,
    ]);

    $this->getJson(route('dashboard.data'))
        ->assertOk()
        ->assertJsonPath('0.tahun', 2024)
        ->assertJsonPath('0.total_penduduk', 100)
        ->assertJsonPath('0.agama.islam', 50);
});
