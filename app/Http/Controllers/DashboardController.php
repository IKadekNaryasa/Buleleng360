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
        $kecamatanList = Kecamatan::get()->sortBy(fn (Kecamatan $kecamatan): int => $this->kecamatanSortPosition($kecamatan->nama))->values()->map(function (Kecamatan $kecamatan): Kecamatan {
            $kecamatan->kode = $this->kecamatanCode($kecamatan->nama);

            return $kecamatan;
        });

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
        ])->get()->sortBy(fn (Kecamatan $kecamatan): int => $this->kecamatanSortPosition($kecamatan->nama))->values()->map(function (Kecamatan $kecamatan): array {
            $desa = $kecamatan->desa;

            return [
                'id' => $kecamatan->id,
                'nama' => $kecamatan->nama,
                'kode' => $this->kecamatanCode($kecamatan->nama),
                'lat' => (float) $kecamatan->lat,
                'long' => (float) $kecamatan->long,
                'total_penduduk' => $desa->flatMap->penduduk->where('tahun', 2025)->sum('total_jiwa'),
                'ormas' => $desa->flatMap(fn ($desaItem) => $desaItem->ormas->map(fn ($item): array => [
                    'nama_desa' => $desaItem->nama,
                    'nama' => $item->nama,
                    'jumlah_anggota' => $item->jumlah_anggota,
                    'ketua' => $item->ketua,
                    'sekretaris' => $item->sekretaris,
                    'bendahara' => $item->bendahara,
                    'alamat' => $item->alamat,
                    'lat' => (float) $item->lat,
                    'long' => (float) $item->long,
                ]))->values(),
                'partai' => $desa->flatMap(fn ($desaItem) => $desaItem->partai->map(fn ($item): array => [
                    'nama_desa' => $desaItem->nama,
                    'nama' => $item->nama,
                    'jumlah_kader' => $item->jumlah_kader,
                    'ketua' => $item->ketua,
                    'sekretaris' => $item->sekretaris,
                    'bendahara' => $item->bendahara,
                    'alamat' => $item->alamat,
                    'lat' => (float) $item->lat,
                    'long' => (float) $item->long,
                ]))->values(),
                'agama' => $desa->flatMap->sebaranAgama
                    ->groupBy('agama.agama')
                    ->map(fn ($rows): int => $rows->sum('jumlah_pemeluk'))
                    ->sortKeys()
                    ->all(),
            ];
        })->values();
    }

    private function kecamatanCode(string $nama): string
    {
        return [
            'Tejakula' => 'TJK',
            'Kubutambahan' => 'KBT',
            'Sawan' => 'SWN',
            'Buleleng' => 'BLL',
            'Sukasada' => 'SSD',
            'Banjar' => 'BJR',
            'Seririt' => 'SRT',
            'Busungbiu' => 'BSB',
            'Gerokgak' => 'GRK',
        ][$nama] ?? strtoupper(substr($nama, 0, 3));
    }

    private function kecamatanSortPosition(string $nama): int
    {
        $position = array_search($nama, [
            'Tejakula',
            'Kubutambahan',
            'Sawan',
            'Buleleng',
            'Sukasada',
            'Banjar',
            'Seririt',
            'Busungbiu',
            'Gerokgak',
        ], true);

        return $position === false ? PHP_INT_MAX : $position;
    }
}
