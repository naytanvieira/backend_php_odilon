<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoJustificativa extends Model
{
    protected $table = 'tipos_justificativa';

    protected $fillable = [
        'name',
    ];
}