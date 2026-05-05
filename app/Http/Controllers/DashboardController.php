<?php

namespace App\Http\Controllers;

use App\Models\PlanilhaProcessamentoLog;
use App\Models\User;
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
}