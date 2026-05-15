<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgDispositivoTerapeutico extends Model
{
    use HasFactory;

    protected $table = 'drg_dispositivos_terapeuticos';

    protected $fillable = [
        'drg_id',
        'ordem',
        'local',
        'dispositivo',
        'periodo_inicial',
        'periodo_final',
        'periodo_inicial_da_passagem_do_cti_associada',
        'periodo_final_da_passagem_do_cti_associada'
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
