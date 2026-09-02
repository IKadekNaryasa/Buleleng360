<?php

use App\Models\Bidang;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Role;
use App\Models\User;

it('keeps the existing desa geojson boundary when the admin saves without changes', function () {
    $role = Role::create(['name' => 'Administrator']);
    $bidang = Bidang::create(['name' => 'Umum']);
    $user = User::factory()->create([
        'role_id' => $role->id,
        'bidang_id' => $bidang->id,
        'nip' => '1234567890',
    ]);

    $kecamatan = Kecamatan::create([
        'nama' => 'Sukasada',
        'lat' => -8.10,
        'long' => 115.10,
        'geojson_boundary' => [
            'type' => 'Polygon',
            'coordinates' => [[[115.1, -8.1], [115.2, -8.1], [115.2, -8.2], [115.1, -8.2], [115.1, -8.1]]],
        ],
    ]);

    $originalBoundary = [
        'type' => 'Polygon',
        'coordinates' => [[[115.15, -8.15], [115.25, -8.15], [115.25, -8.25], [115.15, -8.25], [115.15, -8.15]]],
    ];

    $desa = Desa::create([
        'kecamatan_id' => $kecamatan->id,
        'nama' => 'Pegadungan',
        'lat' => -8.2,
        'long' => 115.15,
        'geojson_boundary' => $originalBoundary,
    ]);

    $blankValues = ['', '   ', 'null', '{}', '[]'];

    foreach ($blankValues as $blankValue) {
        $this->actingAs($user)
            ->from(route('admin.resource.edit', ['resource' => 'desa', 'id' => $desa->id]))
            ->put(route('admin.resource.update', ['resource' => 'desa', 'id' => $desa->id]), [
                'kecamatan_id' => $kecamatan->id,
                'nama' => 'Pegadungan',
                'lat' => -8.2,
                'long' => 115.15,
                'geojson_boundary' => $blankValue,
            ])
            ->assertRedirect(route('admin.resource.index', 'desa'));

        $desa->refresh();
        expect($desa->geojson_boundary)->toBe($originalBoundary);
    }

    $validJsonString = '{"type":"Polygon","coordinates":[[[115.1,-8.1],[115.2,-8.1],[115.2,-8.2],[115.1,-8.2],[115.1,-8.1]]]}';

    $this->actingAs($user)
        ->from(route('admin.resource.edit', ['resource' => 'desa', 'id' => $desa->id]))
        ->put(route('admin.resource.update', ['resource' => 'desa', 'id' => $desa->id]), [
            'kecamatan_id' => $kecamatan->id,
            'nama' => 'Pegadungan',
            'lat' => -8.2,
            'long' => 115.15,
            'geojson_boundary' => $validJsonString,
        ])
        ->assertRedirect(route('admin.resource.index', 'desa'));

    $desa->refresh();
    expect($desa->geojson_boundary)->toBe([
        'type' => 'Polygon',
        'coordinates' => [[[115.1, -8.1], [115.2, -8.1], [115.2, -8.2], [115.1, -8.2], [115.1, -8.1]]],
    ]);
});
