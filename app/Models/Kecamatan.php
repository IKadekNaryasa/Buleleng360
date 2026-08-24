<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'lat', 'long', 'geojson_boundary'])]
class Kecamatan extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'geojson_boundary' => 'array',
        'lat' => 'decimal:7',
        'long' => 'decimal:7',
    ];

    public function desa()
    {
        return $this->hasMany(Desa::class);
    }

    public function totalPenduduk(int $tahun): int
    {
        return Penduduk::whereIn('desa_id', $this->desa->pluck('id'))
            ->where('tahun', $tahun)
            ->sum('total_jiwa');
    }

    public function persentaseAgama(int $tahun): array
    {
        $totalJiwa = $this->totalPenduduk($tahun);

        if (! $totalJiwa) {
            return [];
        }

        $desaIds = $this->desa()->pluck('id');

        return SebaranAgama::whereIn('desa_id', $desaIds)
            ->with('agama')
            ->get()
            ->groupBy('agama.agama')
            ->map(fn ($rows) => round(($rows->sum('jumlah_pemeluk') / $totalJiwa) * 100, 2))
            ->toArray();
    }
}
