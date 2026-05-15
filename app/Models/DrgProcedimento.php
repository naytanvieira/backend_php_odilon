<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgProcedimento extends Model
{
    use HasFactory;

    protected $table = 'drg_procedimentos';

    protected $fillable = [
        'drg_id',
        'ordem',
        'codigo',
        'nome',
        'data_de_execucao',
        'data_final_de_execucao',
        'data_da_solicitacao',
        'data_da_autorizacao'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer'
    ];


    public function medicos(): HasMany
    {
        return $this->hasMany(DrgProcedimentoMedico::class, 'procedimento_id')->orderBy('ordem');
    }

    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
