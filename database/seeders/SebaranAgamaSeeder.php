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
    public function run(): void
    {
        $jumlahPemeluk = [
            'islam' => 120,
            'kristen_protestan' => 20,
            'kristen_katolik' => 15,
            'hindu' => 250,
            'buddha' => 8,
            'khonghucu' => 2,
            'lainnya' => 5,
        ];

        $agamaMap = Agama::all()->keyBy('agama');

        $desaList = Desa::all();

        if ($desaList->isEmpty()) {
            $this->command->warn('Tidak ada desa ditemukan. Pastikan DesaSeeder dijalankan lebih dulu.');
            return;
        }

        $skippedAgama = [];

        foreach ($desaList as $desa) {
            foreach ($jumlahPemeluk as $jenisAgama => $jumlah) {
                $agama = $agamaMap->get($jenisAgama);

                if (!$agama) {
                    $skippedAgama[$jenisAgama] = true;
                    continue;
                }

                \App\Models\SebaranAgama::updateOrCreate(
                    ['agama_id' => $agama->id, 'desa_id' => $desa->id],
                    ['jumlah_pemeluk' => $jumlah],
                );
            }
        }

        if (!empty($skippedAgama)) {
            $this->command->warn('Jenis agama tidak ditemukan di tabel agama: ' . implode(', ', array_keys($skippedAgama)));
        }

        $this->command->info('Berhasil isi sebaran_agama untuk ' . $desaList->count() . ' desa.');
    }
}
