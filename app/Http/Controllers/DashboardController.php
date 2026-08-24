<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $kecamatanList = Kecamatan::orderBy('nama')->get();

        return view('dashboard.index', compact('kecamatanList'));
    }

    public function data(): JsonResponse
    {
        return response()->json($this->dashboardData());
    }

    private function dashboardData(): Collection
    {
        return Kecamatan::with([
            'desa.ormas',
            'desa.partai',
            'desa.penduduk',
            'desa.sebaranAgama.agama',
        ])->orderBy('nama')->get()->map(function (Kecamatan $kecamatan): array {
            $desa = $kecamatan->desa;

            return [
                'id' => $kecamatan->id,
                'nama' => $kecamatan->nama,
                'lat' => (float) $kecamatan->lat,
                'long' => (float) $kecamatan->long,
                'total_penduduk' => $desa->flatMap->penduduk->where('tahun', 2025)->sum('total_jiwa'),
                'ormas' => $desa->flatMap->ormas->map(fn ($item): array => [
                    'nama' => $item->nama,
                    'jumlah_anggota' => $item->jumlah_anggota,
                    'ketua' => $item->ketua,
                    'alamat' => $item->alamat,
                    'lat' => (float) $item->lat,
                    'long' => (float) $item->long,
                ])->values(),
                'partai' => $desa->flatMap->partai->map(fn ($item): array => [
                    'nama' => $item->nama,
                    'jumlah_kader' => $item->jumlah_kader,
                    'ketua' => $item->ketua,
                    'alamat' => $item->alamat,
                    'lat' => (float) $item->lat,
                    'long' => (float) $item->long,
                ])->values(),
                'agama' => $desa->flatMap->sebaranAgama
                    ->groupBy('agama.agama')
                    ->map(fn ($rows): int => $rows->sum('jumlah_pemeluk'))
                    ->sortKeys()
                    ->all(),
            ];
        })->values();
    }
}
