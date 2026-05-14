<?php

namespace App\Actions;

use App\Models\PontoRegistro;
use App\Models\PontoSaldo;
use Carbon\Carbon;

class RecalcularSaldoAction
{
    public function execute($userId)
    {
        $inicioMes = now()->startOfMonth();
        $fimMes = now()->endOfMonth();

        $inicioSemana = now()->startOfWeek();
        $fimSemana = now()->endOfWeek();

        $hoje = now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | REGISTROS DO MÊS
        |--------------------------------------------------------------------------
        */

        $registrosMes = PontoRegistro::where(
            'user_id',
            $userId
        )

            ->whereBetween('registrado_em', [
                $inicioMes,
                $fimMes
            ])

            ->orderBy('registrado_em')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | AGRUPAR POR DIA
        |--------------------------------------------------------------------------
        */

        $registrosPorDia = $registrosMes->groupBy(function ($item) {

            return Carbon::parse(
                $item->registrado_em
            )->format('Y-m-d');
        });

        /*
        |--------------------------------------------------------------------------
        | ACUMULADORES
        |--------------------------------------------------------------------------
        */

        $horasTrabalhadas = 0;

        $horasExtras = 0;

        $faltasAtrasos = 0;

        $diasTrabalhadosSemana = [];

        $horasHoje = 0;

        /*
        |--------------------------------------------------------------------------
        | CALCULAR
        |--------------------------------------------------------------------------
        */

        foreach ($registrosPorDia as $data => $registros) {

            $entrada = $registros
                ->where('tipo_ponto_id', 1)
                ->first();

            $saidaAlmoco = $registros
                ->where('tipo_ponto_id', 2)
                ->first();

            $retornoAlmoco = $registros
                ->where('tipo_ponto_id', 3)
                ->first();

            $saida = $registros
                ->where('tipo_ponto_id', 4)
                ->first();

            if (
                $entrada &&
                $saidaAlmoco &&
                $retornoAlmoco &&
                $saida
            ) {

                /*
                manhã
                */

                $minutosManha =
                    Carbon::parse(
                        $entrada->registrado_em
                    )->diffInMinutes(
                        Carbon::parse(
                            $saidaAlmoco->registrado_em
                        )
                    );

                /*
                tarde
                */

                $minutosTarde =
                    Carbon::parse(
                        $retornoAlmoco->registrado_em
                    )->diffInMinutes(
                        Carbon::parse(
                            $saida->registrado_em
                        )
                    );

                $totalDia =
                    $minutosManha +
                    $minutosTarde;

                $horasTrabalhadas +=
                    $totalDia;

                /*
                META DIÁRIA
                */

                $metaDiaria = 480;

                /*
                EXTRA
                */

                if ($totalDia > $metaDiaria) {

                    $horasExtras +=
                        ($totalDia - $metaDiaria);
                }

                /*
                ATRASO
                */

                if ($totalDia < $metaDiaria) {

                    $faltasAtrasos +=
                        ($metaDiaria - $totalDia);
                }

                /*
                HOJE
                */

                if ($data === $hoje) {

                    $horasHoje =
                        $totalDia;
                }

                /*
                SEMANA
                */

                if (
                    Carbon::parse($data)
                    ->between(
                        $inicioSemana,
                        $fimSemana
                    )
                ) {

                    $diasTrabalhadosSemana[] =
                        Carbon::parse($data)
                        ->translatedFormat('D');
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | HORAS PREVISTAS
        |--------------------------------------------------------------------------
        */

        $diasUteisMes = now()
            ->daysInMonth;

        $horasPrevistas =
            $diasUteisMes * 480;

        /*
        |--------------------------------------------------------------------------
        | SALDO
        |--------------------------------------------------------------------------
        */

        $saldoMinutos =
            $horasTrabalhadas -
            $horasPrevistas;

        /*
        |--------------------------------------------------------------------------
        | SALVAR
        |--------------------------------------------------------------------------
        */

        $saldo = PontoSaldo::updateOrCreate(

            [
                'user_id' => $userId,

                'mes' => now()->month,

                'ano' => now()->year,
            ],

            [
                'saldo_minutos' =>
                    $saldoMinutos,

                'horas_previstas' =>
                    $horasPrevistas,

                'horas_trabalhadas' =>
                    $horasTrabalhadas,

                'horas_extras' =>
                    $horasExtras,

                'faltas_atrasos' =>
                    $faltasAtrasos,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | RESPONSE DATA
        |--------------------------------------------------------------------------
        */

        return [

            /*
            saldo model
            */

            'id' => $saldo->id,

            'saldo_formatado' =>
                $saldo->saldo_formatado,

            'horas_previstas_formatado' =>
                $saldo->horas_previstas_formatado,

            'horas_trabalhadas_formatado' =>
                $saldo->horas_trabalhadas_formatado,

            'horas_extras_formatado' =>
                $saldo->horas_extras_formatado,

            'faltas_atrasos_formatado' =>
                $saldo->faltas_atrasos_formatado,

            /*
            raw
            */

            'saldo_minutos' =>
                $saldo->saldo_minutos,

            'horas_previstas' =>
                $saldo->horas_previstas,

            'horas_trabalhadas' =>
                $saldo->horas_trabalhadas,

            /*
            top cards
            */

            'horas_trabalhadas_hoje_formatado' =>
                $this->formatarMinutos(
                    $horasHoje
                ),

            'meta_diaria_formatado' =>
                '08h 00m',

            'dias_trabalhados_semana' =>
                count($diasTrabalhadosSemana),

            'dias_semana_label' =>
                implode(
                    ' · ',
                    $diasTrabalhadosSemana
                ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | FORMATADOR
    |--------------------------------------------------------------------------
    */

    private function formatarMinutos($minutos)
    {
        $sinal = $minutos < 0 ? '-' : '+';

        $minutos = abs($minutos);

        $horas = floor($minutos / 60);

        $mins = $minutos % 60;

        return sprintf(
            '%s%02dh %02dm',
            $sinal,
            $horas,
            $mins
        );
    }
}