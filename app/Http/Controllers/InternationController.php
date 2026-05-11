<?php

namespace App\Http\Controllers;

use App\Models\Internacoes;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InternationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTAR INTERNACOES
    |--------------------------------------------------------------------------
    */
   public function index(Request $request)
{
    $search  = $request->query('search', '');
    $ativas  = $request->query('ativas', 0);
    $ativas = $ativas === "false" ? false : true;
    $mes    = $request->query('mes', '');
    $ano    = $request->query('ano', '');

   
    $internacoes = Internacoes::with('paciente')
        ->when($search, function ($query) use ($search) {
            $query->where('codigo_atendimento', 'like', "%{$search}%")
                  ->orWhere('tipo_internacao', 'like', "%{$search}%")
                  ->orWhere('leito', 'like', "%{$search}%")
                  ->orWhere('convenio', 'like', "%{$search}%")
                  ->orWhere('medico', 'like', "%{$search}%")
                  ->orWhere('setor', 'like', "%{$search}%")
                  ->orWhereHas('paciente', function ($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
        })
        ->when($ativas, function ($query) {
            $query->whereNull('data_alta');
        })
         ->when($mes, function ($query) use ($mes) {
            $query->whereMonth('dt_interna', $mes);
        })
        ->when($ano, function ($query) use ($ano) {
            $query->whereYear('dt_interna', $ano);
        })
        ->paginate(10);

    return response()->json([
        'data'         => $internacoes->items(),
        'current_page' => $internacoes->currentPage(),
        'last_page'    => $internacoes->lastPage(),
        'total'        => $internacoes->total(),
    ]);
}


public function exportar(Request $request): StreamedResponse
{
    $search = $request->query('search', '');
    $ativas = $request->query('ativas', 0);
    $mes    = $request->query('mes', '');
    $ano    = $request->query('ano', '');

    $internacoes = Internacoes::with('paciente')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo_atendimento', 'like', "%{$search}%")
                  ->orWhere('tipo_internacao', 'like', "%{$search}%")
                  ->orWhere('leito', 'like', "%{$search}%")
                  ->orWhere('convenio', 'like', "%{$search}%")
                  ->orWhere('medico', 'like', "%{$search}%")
                  ->orWhere('setor', 'like', "%{$search}%")
                  ->orWhereHas('paciente', function ($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
            });
        })
        ->when($ativas, function ($query) {
            $query->whereNull('data_alta');
        })
        ->when($mes, function ($query) use ($mes) {
            $query->whereMonth('dt_interna', $mes);
        })
        ->when($ano, function ($query) use ($ano) {
            $query->whereYear('dt_interna', $ano);
        })
        ->get(); // ← sem paginate, pega tudo

    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="internacoes.csv"',
    ];

    $callback = function () use ($internacoes) {
        $file = fopen('php://output', 'w');

        // BOM para Excel reconhecer UTF-8
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Cabeçalho
        fputcsv($file, [
            'Código Atendimento',
            'Código Paciente',
            'Nome Paciente',
            'Tipo Internação',
            'Leito',
            'Data Internação',
            'Data Alta',
            'Qtd Dias',
            'Convênio',
            'Médico',
            'Setor',
        ], ';');

        // Dados
        foreach ($internacoes as $item) {
            fputcsv($file, [
                $item->codigo_atendimento,
                $item->cod_paciente,
                $item->paciente->nome ?? '-',
                $item->tipo_internacao,
                $item->leito,
                $item->dt_interna,
                $item->data_alta ?? '-',
                $item->qtd_dias_int,
                $item->convenio,
                $item->medico,
                $item->setor,
            ], ';');
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

    /*
    |--------------------------------------------------------------------------
    | CADASTRAR INTERNACAO
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'tipo_internacao' => 'nullable|string',
            'leito' => 'nullable|string',
            'dt_interna' => 'nullable|date',
            'data_alta' => 'nullable|date',
            'convenio' => 'nullable|string',
            'medico' => 'nullable|string',
            'setor' => 'nullable|string',
        ]);

        $internacao = Internacoes::create($request->all());

        return response()->json([
            'message' => 'Internação criada com sucesso',
            'data' => $internacao->load('paciente')
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | MOSTRAR UMA INTERNACAO
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $internacao = Internacoes::with('paciente')->findOrFail($id);

        return response()->json([
            'data' => $internacao
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAR INTERNACAO
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $internacao = Internacoes::findOrFail($id);

        $internacao->update($request->all());

        return response()->json([
            'message' => 'Internação atualizada com sucesso',
            'data' => $internacao->load('paciente')
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETAR INTERNACAO
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $internacao = Internacoes::findOrFail($id);

        $internacao->delete();

        return response()->json([
            'message' => 'Internação removida com sucesso'
        ]);
    }
}