<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgFalhaEstruturaProcesso extends Model
{
    use HasFactory;

    protected $table = 'drg_falhas_estrutura_processo';

    protected $fillable = [
        'drg_id',
        'ordem',
        'falha',
        'tempo',
        'data_inicial',
        'data_final',
        'origem'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer',
        'tempo' => 'integer'
    ];


    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
