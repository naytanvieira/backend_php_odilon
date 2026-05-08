<?php


namespace App\Http\Controllers;

use App\Models\PlanilhaProcessamentoLog;
use Illuminate\Http\Request;

class PlanilhaProcessamentoLogController extends Controller
{
    /**
     * Salvar log vindo do Next ou outra API
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_arquivo' => 'required|string',
            'tipo' => 'nullable|string',
            'status' => 'required|in:pendente,processando,sucesso,erro',

            'total_registros' => 'nullable|integer',
            'processados' => 'nullable|integer',
            'erros' => 'nullable|integer',

            'inicio_processamento' => 'nullable|date',
            'fim_processamento' => 'nullable|date',

            'tempo_execucao_ms' => 'nullable|integer',
            'mensagem_erro' => 'nullable|string',

            'user_id' => 'nullable|exists:users,id',
        ]);

        $log = PlanilhaProcessamentoLog::create($data);

        return response()->json([
            'message' => 'Log salvo com sucesso',
            'data' => $log
        ]);
    }

    /**
     * Listar logs para dashboard
     */
    public function index()
    {
        return PlanilhaProcessamentoLog::latest()->paginate(20);
    }


    public function tempoEconomizado()
{
    try {

        $logs = PlanilhaProcessamentoLog::with('spreadsheetType')
            ->get();

        $totalHoras = 0;

        foreach ($logs as $log) {

            /*
            |--------------------------------------------------------------------------
            | manual_time
            |--------------------------------------------------------------------------
            | Ex:
            | "2"
            | "1.5"
            | "3"
            */

            $horas = (float) (
                $log->spreadsheetType?->manual_time ?? 0
            );

            $totalHoras += $horas;
        }

        /*
        |--------------------------------------------------------------------------
        | FORMATAR
        |--------------------------------------------------------------------------
        */
        $horasInteiras = floor($totalHoras);

        $minutos =
            round(($totalHoras - $horasInteiras) * 60);

        return response()->json([

            'total_horas' => $totalHoras,

            'tempo_formatado' =>
                "{$horasInteiras}h {$minutos}m",

            'logs_processados' => $logs->count(),
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Erro ao calcular tempo economizado',
            'error' => $e->getMessage()
        ], 500);
    }
}
    }

