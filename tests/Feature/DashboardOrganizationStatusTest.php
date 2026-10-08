<?php

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Ormas;
use App\Models\Partai;

it('includes only active ormas and partai in dashboard data', function () {
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

    foreach ([
        ['model' => Ormas::class, 'name' => 'Ormas Aktif', 'inactive_name' => 'Ormas Nonaktif'],
        ['model' => Partai::class, 'name' => 'Partai Aktif', 'inactive_name' => 'Partai Nonaktif'],
    ] as $organization) {
        $memberCountColumn = $organization['model'] === Ormas::class ? 'jumlah_anggota' : 'jumlah_kader';

        $organization['model']::create([
            'desa_id' => $desa->id,
            'nama' => $organization['name'],
            $memberCountColumn => 1,
            'ketua' => 'Ketua',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'lat' => -8.2,
            'long' => 115.15,
            'status' => 'aktif',
        ]);

        $organization['model']::create([
            'desa_id' => $desa->id,
            'nama' => $organization['inactive_name'],
            $memberCountColumn => 1,
            'ketua' => 'Ketua',
            'sekretaris' => 'Sekretaris',
            'bendahara' => 'Bendahara',
            'lat' => -8.2,
            'long' => 115.15,
            'status' => 'nonaktif',
        ]);
    }

    $this->getJson(route('dashboard.data'))
        ->assertOk()
        ->assertJsonCount(1, '0.ormas')
        ->assertJsonCount(1, '0.partai')
        ->assertJsonPath('0.ormas.0.nama', 'Ormas Aktif')
        ->assertJsonPath('0.partai.0.nama', 'Partai Aktif')
        ->assertJsonMissing(['nama' => 'Ormas Nonaktif'])
        ->assertJsonMissing(['nama' => 'Partai Nonaktif']);
});
