<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgSuporteVentilatorio extends Model
{
    use HasFactory;

    protected $table = 'drg_suportes_ventilatorios';

    protected $fillable = [
        'drg_id',
        'ordem',
        'tipo',
        'tipo_invasivo',
        'local',
        'data_inicial',
        'data_final'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer',
        'tipo_invasivo' => 'boolean'
    ];


    public function condicoes(): HasMany
    {
        return $this->hasMany(DrgSuporteCondicao::class, 'suporte_ventilatorio_id')->orderBy('contexto')->orderBy('ordem');
    }

    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
