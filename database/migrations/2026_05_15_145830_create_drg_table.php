<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $addNullableColumns = function (Blueprint $table, array $columns): void {
            foreach ($columns as $type => $names) {
                foreach ($names as $name) {
                    $table->{$type}($name)->nullable();
                }
            }
        };

        Schema::create('drg', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();

            $addNullableColumns($table, [
                'text' => [
                    'identificador_do_paciente',
                    'plano',
                    'codigo_da_instituicao',
                    'nome_da_instituicao',
                    'codigo_do_paciente',
                    'data_de_nascimento',
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
                    'data_da_autorizacao',
                    'data_de_internacao',
                    'data_prevista_da_alta',
                    'data_da_alta',
                    'condicao_da_alta',
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
                    'acomodacao',
                    'cid_principal',
                    'descricao_do_cid_principal',
                    'internacao_sensivel_ao_cuidado_primario',
                    'ventilacao_mecanica',
                    'total_de_horas_com_ventilacao_mecanica',
                    'codigo',
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
                    'cnes_do_hospital_de_internacao',
                ],
                'integer' => [
                    'idade_em_anos',
                    'idade_em_meses',
                    'idade_em_dias',
                    'modalidade_de_cuidado_na_procedencia',
                    'modalidade_de_cuidado_na_alta',
                    'numero_do_registro',
                    'numero_de_atendimento',
                    'numero_da_autorizacao',
                    'modalidade_da_internacao',
                    'peso_do_drg_brasil_refinado',
                ],
                'boolean' => [
                    'paciente_internado_outras_vezes',
                ],
            ]);

            $table->timestamps();
        });

        Schema::create('drg_cids_secundarios', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'codigo',
                    'nome',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_cids_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_cids_drg_ordem_unique');
        });

        Schema::create('drg_medicos', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'nome',
                    'uf',
                    'tipo_de_atuacao',
                    'responsavel_pelo_paciente',
                ],
                'integer' => [
                    'crm',
                    'especialidade',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_medicos_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_medicos_drg_ordem_unique');
        });

        Schema::create('drg_procedimentos', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'codigo',
                    'nome',
                    'data_de_execucao',
                    'data_final_de_execucao',
                    'data_da_solicitacao',
                    'data_da_autorizacao',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_proc_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_proc_drg_ordem_unique');
        });

        Schema::create('drg_procedimento_medicos', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('procedimento_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'nome',
                    'uf',
                    'tipo_de_atuacao',
                ],
                'integer' => [
                    'crm',
                    'especialidade',
                ],
            ]);

            $table->timestamps();

            $table->foreign('procedimento_id', 'drg_proc_med_proc_fk')
                ->references('id')
                ->on('drg_procedimentos')
                ->onDelete('cascade');

            $table->unique(['procedimento_id', 'ordem'], 'drg_proc_med_proc_ordem_unique');
        });

        Schema::create('drg_condicoes_adquiridas', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'data_de_ocorrencia',
                    'codigo',
                    'descricao',
                    'medico_responsavel',
                    'data_da_manifestacao',
                    'grave',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_ca_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_ca_drg_ordem_unique');
        });

        Schema::create('drg_suportes_ventilatorios', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'tipo',
                    'local',
                    'data_inicial',
                    'data_final',
                ],
                'boolean' => [
                    'tipo_invasivo',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_suporte_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_suporte_drg_ordem_unique');
        });

        Schema::create('drg_suporte_condicoes', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('suporte_ventilatorio_id');
            $table->string('contexto', 30);
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'data_da_ocorrencia',
                    'codigo_da_ca',
                    'descricao_da_condicao_adquirida',
                ],
            ]);

            $table->timestamps();

            $table->foreign('suporte_ventilatorio_id', 'drg_suporte_ca_suporte_fk')
                ->references('id')
                ->on('drg_suportes_ventilatorios')
                ->onDelete('cascade');

            $table->unique(
                ['suporte_ventilatorio_id', 'contexto', 'ordem'],
                'drg_suporte_ca_unique'
            );
        });

        Schema::create('drg_sondas_vesicais', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'local',
                    'data_inicial',
                    'data_final',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_sonda_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_sonda_drg_ordem_unique');
        });

        Schema::create('drg_sonda_condicoes', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('sonda_vesical_id');
            $table->string('contexto', 30);
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'data_da_ocorrencia',
                    'codigo_da_ca',
                    'descricao_da_condicao_adquirida',
                ],
            ]);

            $table->timestamps();

            $table->foreign('sonda_vesical_id', 'drg_sonda_ca_sonda_fk')
                ->references('id')
                ->on('drg_sondas_vesicais')
                ->onDelete('cascade');

            $table->unique(
                ['sonda_vesical_id', 'contexto', 'ordem'],
                'drg_sonda_ca_unique'
            );
        });

        Schema::create('drg_cateteres_centrais', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'local',
                    'data_inicial',
                    'data_final',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_cateter_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_cateter_drg_ordem_unique');
        });

        Schema::create('drg_cateter_condicoes', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('cateter_central_id');
            $table->string('contexto', 30);
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'data_da_ocorrencia',
                    'codigo_da_ca',
                    'descricao_da_condicao_adquirida',
                ],
            ]);

            $table->timestamps();

            $table->foreign('cateter_central_id', 'drg_cateter_ca_cateter_fk')
                ->references('id')
                ->on('drg_cateteres_centrais')
                ->onDelete('cascade');

            $table->unique(
                ['cateter_central_id', 'contexto', 'ordem'],
                'drg_cateter_ca_unique'
            );
        });

        Schema::create('drg_dispositivos_terapeuticos', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'local',
                    'dispositivo',
                    'periodo_inicial',
                    'periodo_final',
                    'periodo_inicial_da_passagem_do_cti_associada',
                    'periodo_final_da_passagem_do_cti_associada',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_disp_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_disp_drg_ordem_unique');
        });

        Schema::create('drg_analises', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'data',
                    'descricao',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_analises_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_analises_drg_ordem_unique');
        });

        Schema::create('drg_falhas_estrutura_processo', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'falha',
                    'data_inicial',
                    'data_final',
                    'origem',
                ],
                'integer' => [
                    'tempo',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_falhas_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_falhas_drg_ordem_unique');
        });

        Schema::create('drg_recem_nascidos', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'comprimento',
                    'sexo',
                    'quinto_minuto',
                    'recebeu_alta_para_casa_domicilio_em_ate_48_horas',
                ],
                'integer' => [
                    'peso_de_nascimento',
                    'idade_gestacional',
                ],
                'boolean' => [
                    'nascido_vivo',
                    'houve_tocotraumatismo',
                    'mediu_apgar',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_rn_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_rn_drg_ordem_unique');
        });

        Schema::create('drg_ctis', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'data_inicial_de_internacao',
                    'leito',
                    'data_final_de_internacao',
                    'nome_do_medico',
                    'uf_do_medico',
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
                    'permanencia_real',
                ],
                'integer' => [
                    'crm_do_medico',
                    'especialidade_do_medico',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_cti_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_cti_drg_ordem_unique');
        });

        Schema::create('drg_altas_administrativas', function (Blueprint $table) use ($addNullableColumns) {
            $table->id();
            $table->unsignedBigInteger('drg_id');
            $table->unsignedTinyInteger('ordem');

            $addNullableColumns($table, [
                'text' => [
                    'data_da_autorizacao',
                    'data_do_atendimento_inicial',
                    'data_do_atendimento_final',
                ],
                'integer' => [
                    'numero_do_atendimento',
                    'numero_da_autorizacao',
                ],
            ]);

            $table->timestamps();

            $table->foreign('drg_id', 'drg_alta_adm_drg_fk')->references('id')->on('drg')->onDelete('cascade');
            $table->unique(['drg_id', 'ordem'], 'drg_alta_adm_drg_ordem_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drg_altas_administrativas');
        Schema::dropIfExists('drg_ctis');
        Schema::dropIfExists('drg_recem_nascidos');
        Schema::dropIfExists('drg_falhas_estrutura_processo');
        Schema::dropIfExists('drg_analises');
        Schema::dropIfExists('drg_dispositivos_terapeuticos');
        Schema::dropIfExists('drg_cateter_condicoes');
        Schema::dropIfExists('drg_cateteres_centrais');
        Schema::dropIfExists('drg_sonda_condicoes');
        Schema::dropIfExists('drg_sondas_vesicais');
        Schema::dropIfExists('drg_suporte_condicoes');
        Schema::dropIfExists('drg_suportes_ventilatorios');
        Schema::dropIfExists('drg_condicoes_adquiridas');
        Schema::dropIfExists('drg_procedimento_medicos');
        Schema::dropIfExists('drg_procedimentos');
        Schema::dropIfExists('drg_medicos');
        Schema::dropIfExists('drg_cids_secundarios');
        Schema::dropIfExists('drg');
    }
};
