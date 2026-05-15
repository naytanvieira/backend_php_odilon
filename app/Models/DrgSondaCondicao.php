<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgSondaCondicao extends Model
{
    use HasFactory;

    protected $table = 'drg_sonda_condicoes';

    protected $fillable = [
        'sonda_vesical_id',
        'contexto',
        'ordem',
        'data_da_ocorrencia',
        'codigo_da_ca',
        'descricao_da_condicao_adquirida'
    ];

    protected $casts = [
        'sonda_vesical_id' => 'integer',
        'ordem' => 'integer'
    ];


    public function sondaVesical(): BelongsTo
    {
        return $this->belongsTo(DrgSondaVesical::class, 'sonda_vesical_id');
    }
}
