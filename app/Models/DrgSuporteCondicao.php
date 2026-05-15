<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgSuporteCondicao extends Model
{
    use HasFactory;

    protected $table = 'drg_suporte_condicoes';

    protected $fillable = [
        'suporte_ventilatorio_id',
        'contexto',
        'ordem',
        'data_da_ocorrencia',
        'codigo_da_ca',
        'descricao_da_condicao_adquirida'
    ];

    protected $casts = [
        'suporte_ventilatorio_id' => 'integer',
        'ordem' => 'integer'
    ];


    public function suporteVentilatorio(): BelongsTo
    {
        return $this->belongsTo(DrgSuporteVentilatorio::class, 'suporte_ventilatorio_id');
    }
}
