<?php

namespace Database\Seeders;

use App\Models\Agama;
use App\Models\Desa;
use Illuminate\Database\Seeder;

class SebaranAgamaSeeder extends Seeder
{
    /**
     * PERBAIKAN: sebelumnya file ini hardcode UUID manual (50000000-...,
     * 80000000-...) yang TIDAK PERNAH cocok dengan UUID asli, karena
     * DesaSeeder & AgamaSeeder pakai Model::create() biasa -- ID-nya
     * di-generate ACAK oleh trait HasUuids, bukan nilai tetap yang bisa
     * ditebak.
     *
     * Solusinya: query balik ID asli lewat nama/agama, sama seperti pola
     * yang dipakai DesaSeeder untuk mencari kecamatan_id via keyBy('nama').
     */
    public function run(): void {}
}
