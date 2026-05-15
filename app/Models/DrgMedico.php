<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgMedico extends Model
{
    use HasFactory;

    protected $table = 'drg_medicos';

    protected $fillable = [
        'drg_id',
        'ordem',
        'nome',
        'uf',
        'crm',
        'especialidade',
        'tipo_de_atuacao',
        'responsavel_pelo_paciente'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer',
        'crm' => 'integer',
        'especialidade' => 'integer'
    ];


    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
