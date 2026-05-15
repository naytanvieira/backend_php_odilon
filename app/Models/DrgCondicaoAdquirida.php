<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgCondicaoAdquirida extends Model
{
    use HasFactory;

    protected $table = 'drg_condicoes_adquiridas';

    protected $fillable = [
        'drg_id',
        'ordem',
        'data_de_ocorrencia',
        'codigo',
        'descricao',
        'medico_responsavel',
        'data_da_manifestacao',
        'grave'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer'
    ];


    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
