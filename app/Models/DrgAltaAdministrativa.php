<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgAltaAdministrativa extends Model
{
    use HasFactory;

    protected $table = 'drg_altas_administrativas';

    protected $fillable = [
        'drg_id',
        'ordem',
        'numero_do_atendimento',
        'numero_da_autorizacao',
        'data_da_autorizacao',
        'data_do_atendimento_inicial',
        'data_do_atendimento_final'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer',
        'numero_do_atendimento' => 'integer',
        'numero_da_autorizacao' => 'integer'
    ];


    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
