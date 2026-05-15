<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Drg extends Model
{
    use HasFactory;

    protected $table = 'drg';

    protected $fillable = [
        'identificador_do_paciente',
        'plano',
        'codigo_da_instituicao',
        'nome_da_instituicao',
        'codigo_do_paciente',
        'data_de_nascimento',
        'idade_em_anos',
        'idade_em_meses',
        'idade_em_dias',
        'sexo',
        'fonte_pagadora',
        'registro_de_paciente_da_mae',
        'mae_nao_identificada_no_drg_brasil',
        'uf',
        'municipio',
        'situacao_da_internacao',
        'leito',
        'carater_de_internacao',
        'procedencia_do_paciente',
        'modalidade_de_cuidado_na_procedencia',
        'modalidade_de_cuidado_na_alta',
        'numero_do_registro',
        'numero_de_atendimento',
        'numero_da_autorizacao',
        'data_da_autorizacao',
        'data_de_internacao',
        'data_prevista_da_alta',
        'data_da_alta',
        'condicao_da_alta',
        'modalidade_da_internacao',
        'hospital_de_internacao',
        'nome_do_hospital',
        'permanencia_prevista_internacao',
        'codigo_do_drg_admissional',
        'descricao_do_drg_admissional',
        'permanencia_prevista_na_alta',
        'percentil',
        'permanencia_real',
        'codigo_do_drg_brasil_refinado',
        'descricao_do_drg_brasil_refinado',
        'codigo_do_mdc',
        'descricao_do_mdc',
        'tipo_de_drg',
        'peso_do_drg_brasil_refinado',
        'acomodacao',
        'cid_principal',
        'descricao_do_cid_principal',
        'internacao_sensivel_ao_cuidado_primario',
        'ventilacao_mecanica',
        'total_de_horas_com_ventilacao_mecanica',
        'codigo',
        'paciente_internado_outras_vezes',
        'hospital_anterior',
        'uultima_internacao_a_30_dias_ou_menos',
        'internacao_e_uma_complicacao_ou_recaida_da_internacao_anterior',
        'internacao_e_responsavel_por_readmissao_em_30_dias',
        'internacao_e_responsavel_pela_complicacao_ou_recaida',
        'identificador_da_internacao_da_readmissao',
        'data_do_cadastro',
        'usuario_de_cadastro',
        'data_de_cadastro_da_alta',
        'usuario_de_cadastro_da_alta',
        'data_da_uultima_alteracao',
        'usuario_uultima_alteracao',
        'correcao_registro',
        'data_da_correcao',
        'usuario_da_correcao',
        'cnes_do_hospital_de_internacao'
    ];

    protected $casts = [
        'idade_em_anos' => 'integer',
        'idade_em_meses' => 'integer',
        'idade_em_dias' => 'integer',
        'modalidade_de_cuidado_na_procedencia' => 'integer',
        'modalidade_de_cuidado_na_alta' => 'integer',
        'numero_do_registro' => 'integer',
        'numero_de_atendimento' => 'integer',
        'numero_da_autorizacao' => 'integer',
        'modalidade_da_internacao' => 'integer',
        'peso_do_drg_brasil_refinado' => 'decimal:4',
        'paciente_internado_outras_vezes' => 'boolean'
    ];


    public function cidsSecundarios(): HasMany
    {
        return $this->hasMany(DrgCidSecundario::class)->orderBy('ordem');
    }

    public function medicos(): HasMany
    {
        return $this->hasMany(DrgMedico::class)->orderBy('ordem');
    }

    public function procedimentos(): HasMany
    {
        return $this->hasMany(DrgProcedimento::class)->orderBy('ordem');
    }

    public function condicoesAdquiridas(): HasMany
    {
        return $this->hasMany(DrgCondicaoAdquirida::class)->orderBy('ordem');
    }

    public function suportesVentilatorios(): HasMany
    {
        return $this->hasMany(DrgSuporteVentilatorio::class)->orderBy('ordem');
    }

    public function sondasVesicais(): HasMany
    {
        return $this->hasMany(DrgSondaVesical::class)->orderBy('ordem');
    }

    public function cateteresCentrais(): HasMany
    {
        return $this->hasMany(DrgCateterCentral::class)->orderBy('ordem');
    }

    public function dispositivosTerapeuticos(): HasMany
    {
        return $this->hasMany(DrgDispositivoTerapeutico::class)->orderBy('ordem');
    }

    public function analises(): HasMany
    {
        return $this->hasMany(DrgAnalise::class)->orderBy('ordem');
    }

    public function falhasEstruturaProcesso(): HasMany
    {
        return $this->hasMany(DrgFalhaEstruturaProcesso::class)->orderBy('ordem');
    }

    public function recemNascidos(): HasMany
    {
        return $this->hasMany(DrgRecemNascido::class)->orderBy('ordem');
    }

    public function ctis(): HasMany
    {
        return $this->hasMany(DrgCti::class)->orderBy('ordem');
    }

    public function altasAdministrativas(): HasMany
    {
        return $this->hasMany(DrgAltaAdministrativa::class)->orderBy('ordem');
    }
}
