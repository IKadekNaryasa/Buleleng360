<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'lat', 'long', 'kecamatan_id', 'geojson_boundary'])]
class Desa extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'geojson_boundary' => 'array',
        'lat' => 'decimal:7',
        'long' => 'decimal:7',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function penduduk()
    {
        return $this->hasMany(Penduduk::class);
    }

    public function sebaranAgama()
    {
        return $this->hasMany(SebaranAgama::class);
    }

    public function ormas()
    {
        return $this->hasMany(Ormas::class);
    }

    public function partai()
    {
        return $this->hasMany(Partai::class);
    }
}
