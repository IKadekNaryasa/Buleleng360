<?php

use App\Models\Agama;
use App\Models\Bidang;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Role;
use App\Models\SebaranAgama;
use App\Models\User;

it('accepts and persists the year when creating sebaran agama', function () {
    $role = Role::create(['name' => 'Operator']);
    $bidang = Bidang::create(['name' => 'Umum']);
    $user = User::factory()->create([
        'role_id' => $role->id,
        'bidang_id' => $bidang->id,
        'nip' => '1234567890',
    ]);
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

    $this->actingAs($user)
        ->post(route('operator.resource.store', 'sebaran-agama'), [
            'desa_id' => $desa->id,
            'agama_id' => $agama->id,
            'jumlah_pemeluk' => 50,
            'tahun' => 2024,
        ])
        ->assertRedirect(route('operator.resource.index', 'sebaran-agama'));

    expect(SebaranAgama::query()->firstOrFail()->tahun)->toBe(2024);
});
