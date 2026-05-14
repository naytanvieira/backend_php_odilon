<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientController extends Controller
{

public function queryAll(Request $request)
{
    try {
        $search = $request->query('search', '');
        $sexo   = $request->query('sexo', '');
        $cidade = $request->query('cidade', '');

        /* =========================
        QUERY BASE
        ========================= */

        $query = Paciente::query();

        $query->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('paciente', 'like', "%{$search}%")
                ->orWhere('nome', 'like', "%{$search}%");

            });

        });

        $query->when($sexo, function ($query) use ($sexo) {

            $query->where('sexo', $sexo);

        });

        $query->when($cidade, function ($query) use ($cidade) {

            $query->where('cidade', $cidade);

        });

        /* =========================
        LISTAGEM
        ========================= */

        $patients = (clone $query)->paginate(10);

        /* =========================
        STATS
        ========================= */

        $totalMasculino = (clone $query)
            ->where('sexo', 'Masculino')
            ->count();

        $totalFeminino = (clone $query)
            ->where('sexo', 'Feminino')
            ->count();

        /* =========================
        GRÁFICO CIDADES
        ========================= */

        $cidades = (clone $query)

            ->selectRaw('cidade as name, COUNT(*) as total')

            ->whereNotNull('cidade')

            ->groupBy('cidade')

            ->orderByDesc('total')

            ->limit(10)

            ->get();

        
        $faixaEtaria = [
            [
                'name'  => 'Crianças',
                'total' => (clone $query)
                    ->whereRaw('TIMESTAMPDIFF(YEAR, dt_nasc, CURDATE()) BETWEEN 0 AND 12')
                    ->count()
            ],

            [
                'name'  => 'Adolescentes',
                'total' => (clone $query)
                    ->whereRaw('TIMESTAMPDIFF(YEAR, dt_nasc, CURDATE()) BETWEEN 13 AND 17')
                    ->count()
            ],

            [
                'name'  => 'Adultos',
                'total' => (clone $query)
                    ->whereRaw('TIMESTAMPDIFF(YEAR, dt_nasc, CURDATE()) BETWEEN 18 AND 59')
                    ->count()
            ],

            [
                'name'  => 'Idosos',
                'total' => (clone $query)
                    ->whereRaw('TIMESTAMPDIFF(YEAR, dt_nasc, CURDATE()) >= 60')
                    ->count()
            ],
        ];

        /* =========================
        RESPONSE
        ========================= */

        return response()->json([

            'data' => $patients->items(),

            'current_page' => $patients->currentPage(),

            'last_page' => $patients->lastPage(),

            'total' => $patients->total(),

            'stats' => [

                'masculino' => $totalMasculino,

                'feminino' => $totalFeminino,

            ],

            'cidades' => $cidades,
            'faixa_etaria'  => $faixaEtaria,


        ]);

        // return response()->json([
        //     'data' => $patients
        // ], 200);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Erro ao buscar pacientes',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function cidades()
{
    $cidades = Paciente::select('cidade')
        ->whereNotNull('cidade')
        ->distinct()
        ->orderBy('cidade')
        ->pluck('cidade');

    return response()->json($cidades);
}


public function exportar(Request $request): StreamedResponse
{
    $search  = $request->query('search', '');
    $sexo    = $request->query('sexo', '');
    $cidade  = $request->query('cidade', '');

    $patients = Paciente::query()

        ->when($search, function ($query) use ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('paciente', 'like', "%{$search}%")
                  ->orWhere('nome', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%")
                  ->orWhere('bairro', 'like', "%{$search}%")
                  ->orWhere('cidade', 'like', "%{$search}%")
                  ->orWhere('sexo', 'like', "%{$search}%");

            });

        })

        ->when($sexo, function ($query) use ($sexo) {

            $query->where('sexo', $sexo);

        })

        ->when($cidade, function ($query) use ($cidade) {

            $query->where('cidade', $cidade);

        })

        ->orderBy('nome')

        ->get();

    $headers = [
        'Content-Type'        => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="pacientes.csv"',
    ];

    $callback = function () use ($patients) {

        $file = fopen('php://output', 'w');

        // UTF-8 Excel
        fprintf(
            $file,
            chr(0xEF) . chr(0xBB) . chr(0xBF)
        );

        // Cabeçalho
        fputcsv($file, [
            'Atendimento',
            'Paciente',
            'Recepção',
            'Nome',
            'Telefone',
            'Bairro',
            'Cidade',
            'Sexo',
            'Data Nascimento',
        ], ';');

        // Dados
        foreach ($patients as $item) {

            fputcsv($file, [

                $item->atendimento,
                $item->paciente,
                $item->recepcao,
                $item->nome,
                $item->telefone,
                $item->bairro,
                $item->cidade,
                $item->sexo,
                $item->dt_nasc,

            ], ';');

        }

        fclose($file);

    };

    return response()->stream(
        $callback,
        200,
        $headers
    );
}


  
}