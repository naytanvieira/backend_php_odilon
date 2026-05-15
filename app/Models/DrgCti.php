<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrgCti extends Model
{
    use HasFactory;

    protected $table = 'drg_ctis';

    protected $fillable = [
        'drg_id',
        'ordem',
        'data_inicial_de_internacao',
        'leito',
        'data_final_de_internacao',
        'nome_do_medico',
        'uf_do_medico',
        'crm_do_medico',
        'especialidade_do_medico',
        'hospital_de_internacao',
        'nome_do_hospital',
        'tipo_de_cti',
        'condicao_da_alta',
        'cid_principal',
        'descricao_do_cid_principal',
        'codigo_do_drg_brasil_refinado',
        'descricao_do_drg_brasil_refinado',
        'tipo_de_drg',
        'permanencia_prevista_na_alta',
        'permanencia_real'
    ];

    protected $casts = [
        'drg_id' => 'integer',
        'ordem' => 'integer',
        'crm_do_medico' => 'integer',
        'especialidade_do_medico' => 'integer'
    ];


    public function drg(): BelongsTo
    {
        return $this->belongsTo(Drg::class);
    }
}
