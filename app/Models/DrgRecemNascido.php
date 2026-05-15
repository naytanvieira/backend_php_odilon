<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgRecemNascido extends Model
{
    use HasFactory;

    protected $table = 'drg_recem_nascidos';

    protected $fillable = [
        'drg_id',
        'ordem',
        'peso_de_nascimento',
        'idade_gestacional',
        'comprimento',
        'nascido_vivo',
        'sexo',
        'houve_tocotraumatismo',
        'mediu_apgar',
        'quinto_minuto',
        'recebeu_alta_para_casa_domicilio_em_ate_48_horas'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer',
        'peso_de_nascimento' => 'integer',
        'idade_gestacional' => 'integer',
        'nascido_vivo' => 'boolean',
        'houve_tocotraumatismo' => 'boolean',
        'mediu_apgar' => 'boolean'
    ];


    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
