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
    $setor = $request->query('setor', '');

   
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
        ->when($setor, function ($query) use ($setor) {
            $query->where('setor', $setor);
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
    $setor = $request->query('setor', '');

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
        ->when($setor, function ($query) use ($setor) {
            $query->where('setor', $setor);
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

public function setores()
{
    $setores = Internacoes::select('setor')
        ->whereNotNull('setor')
        ->distinct()
        ->orderBy('setor')
        ->pluck('setor');

    return response()->json($setores);
}

public function stats(Request $request)
{
    $search = $request->search;
    $ativas = filter_var($request->ativas, FILTER_VALIDATE_BOOLEAN);
    $mes    = $request->mes;
    $ano    = $request->ano;
    $setor  = $request->setor;

    /* =========================
       QUERY BASE
    ========================= */
    $query = Internacoes::query();

    // SEARCH
    if (!empty($search)) {

        $query->where(function ($q) use ($search) {

            $q->where('codigo_atendimento', 'like', "%{$search}%")
              ->orWhere('cod_paciente', 'like', "%{$search}%")
              ->orWhere('convenio', 'like', "%{$search}%")
              ->orWhere('medico', 'like', "%{$search}%")
              ->orWhere('setor', 'like', "%{$search}%");

        });
    }

    // SOMENTE ATIVAS
    if ($ativas) {
        $query->whereNull('data_alta');
    }

    // MÊS
    if (!empty($mes)) {
        $query->whereMonth('dt_interna', $mes);
    }

    // ANO
    if (!empty($ano)) {
        $query->whereYear('dt_interna', $ano);
    }

    // SETOR
    if (!empty($setor)) {
        $query->where('setor', $setor);
    }

    /* =========================
       SETORES
    ========================= */
    $setores = (clone $query)
        ->selectRaw('setor as name, COUNT(*) as total')
        ->groupBy('setor')
        ->orderByDesc('total')
        ->get();

    /* =========================
       CONVÊNIOS
    ========================= */
    $convenios = (clone $query)
        ->selectRaw('convenio as name, COUNT(*) as total')
        ->groupBy('convenio')
        ->orderByDesc('total')
        ->get();

    /* =========================
       TIPOS
    ========================= */
    $tipos = (clone $query)
        ->selectRaw('tipo_internacao as name, COUNT(*) as total')
        ->groupBy('tipo_internacao')
        ->orderByDesc('total')
        ->get();

    /* =========================
       KPI
    ========================= */
    $internacoesAtivas = (clone $query)
        ->whereNull('data_alta')
        ->count();

    $altas = (clone $query)
        ->whereNotNull('data_alta')
        ->count();

    $faixaEtaria = [
        [
            'name'  => 'Crianças',
            'total' => (clone $query)
                ->whereRaw('TIMESTAMPDIFF(YEAR, data_nasc, CURDATE()) BETWEEN 0 AND 12')
                ->count()
        ],

        [
            'name'  => 'Adolescentes',
            'total' => (clone $query)
                ->whereRaw('TIMESTAMPDIFF(YEAR, data_nasc, CURDATE()) BETWEEN 13 AND 17')
                ->count()
        ],

        [
            'name'  => 'Adultos',
            'total' => (clone $query)
                ->whereRaw('TIMESTAMPDIFF(YEAR, data_nasc, CURDATE()) BETWEEN 18 AND 59')
                ->count()
        ],

        [
            'name'  => 'Idosos',
            'total' => (clone $query)
                ->whereRaw('TIMESTAMPDIFF(YEAR, data_nasc, CURDATE()) >= 60')
                ->count()
        ],
    ];

    /* =========================
       RESPONSE
    ========================= */
    return response()->json([
        'setores'             => $setores,
        'faixa_etaria'        => $faixaEtaria,
        'tipos'               => $tipos,
        'internacoes_ativas'  => $internacoesAtivas,
        'altas'               => $altas,
    ]);
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