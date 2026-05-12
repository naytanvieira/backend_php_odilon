<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Internacoes extends Model
{
    protected $table = 'internacoes';

    protected $fillable = [
        'cod_paciente',
        'codigo_atendimento',
        'tipo_internacao',
        'leito',
        'dt_interna',
        'data_alta',
        'convenio',
        'medico',
        'setor',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTO
    |--------------------------------------------------------------------------
    */
    public function paciente()
    {
        return $this->belongsTo(
        Paciente::class,
        'cod_paciente',                 // coluna da tabela logs
        'paciente'   // coluna da spreadsheet_types
    );
        //return $this->belongsTo(Paciente::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR - DIAS INTERNADO AUTOMÁTICO
    |--------------------------------------------------------------------------
    */
    public function getQtdDiasIntAttribute()
    {
        if (!$this->dt_interna) {
            return null;
        }

        $inicio = Carbon::parse($this->dt_interna);
        $fim = $this->data_alta
            ? Carbon::parse($this->data_alta)
            : Carbon::now();

        return $inicio->diffInDays($fim);
    }
}