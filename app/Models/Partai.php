<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;


#[Fillable([
    'nama_partai',
    'jumlah_kader',
    'ketua',
    'sekretaris',
    'bendahara',
    'desa_id',
    'lat',
    'long',
    'alamat',
])]
class Partai extends Model
{
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'lat' => 'decimal:7',
        'long' => 'decimal:7',
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
