<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgProcedimentoMedico extends Model
{
    use HasFactory;

    protected $table = 'drg_procedimento_medicos';

    protected $fillable = [
        'procedimento_id',
        'ordem',
        'nome',
        'uf',
        'crm',
        'especialidade',
        'tipo_de_atuacao'
    ];

    protected $casts = [
        'procedimento_id' => 'integer',
        'ordem' => 'integer',
        'crm' => 'integer',
        'especialidade' => 'integer'
    ];


    public function procedimento(): BelongsTo
    {
        return $this->belongsTo(DrgProcedimento::class, 'procedimento_id');
    }
}
