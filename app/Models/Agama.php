<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;


#[Fillable(['agama'])]
class Agama extends Model
{
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;

    public function sebaranAgama()
    {
        return $this->hasMany(SebaranAgama::class);
    }
}
