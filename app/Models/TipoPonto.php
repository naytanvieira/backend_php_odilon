<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPonto extends Model
{
    protected $table = 'tipos_ponto';

    protected $fillable = [
        'name',
    ];
}