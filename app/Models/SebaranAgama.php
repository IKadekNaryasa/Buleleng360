<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['desa_id', 'agama_id', 'jumlah_pemeluk'])]
class SebaranAgama extends Model
{
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function agama()
    {
        return $this->belongsTo(Agama::class);
    }
}
