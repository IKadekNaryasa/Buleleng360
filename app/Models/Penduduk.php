<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['desa_id', 'total_jiwa', 'tahun'])]
class Penduduk extends Model
{
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
