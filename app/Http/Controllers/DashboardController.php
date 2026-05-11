<?php

namespace App\Http\Controllers;

use App\Models\PlanilhaProcessamentoLog;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Internacoes;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function metrics()
    {
        try {
        //return 'teste';
        $today = Carbon::today();

        return response()->json([
           'data' => [
                // total geral
                'total_relatorios' => PlanilhaProcessamentoLog::count(),

                // hoje
                'relatorios_hoje' => PlanilhaProcessamentoLog::whereDate('created_at', $today)->count(),

                // usuários
                'total_usuarios' => User::count(),
           ]
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar dados do dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DashboardController.php
    public function resumo()
    {
        $pacientes         = Paciente::count();
        $internacoesAtivas = Internacoes::whereNull('data_alta')->count();
        $altas             = Internacoes::whereNotNull('data_alta')->count();
        $totalLeitos       = 100; // ajusta para o seu valor real
        $ocupacao          = $totalLeitos > 0 
                                ? round(($internacoesAtivas / $totalLeitos) * 100) 
                                : 0;

        return response()->json([
            'pacientes'   => $pacientes,
            'internacoes' => $internacoesAtivas,
            'altas'       => $altas,
            'ocupacao'    => $ocupacao,
        ]);
    }
}