<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgCateterCondicao extends Model
{
    use HasFactory;

    protected $table = 'drg_cateter_condicoes';

    protected $fillable = [
        'cateter_central_id',
        'contexto',
        'ordem',
        'data_da_ocorrencia',
        'codigo_da_ca',
        'descricao_da_condicao_adquirida'
    ];

    protected $casts = [
        'cateter_central_id' => 'integer',
        'ordem' => 'integer'
    ];


    public function cateterCentral(): BelongsTo
    {
        return $this->belongsTo(DrgCateterCentral::class, 'cateter_central_id');
    }
}
