<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PatientController extends Controller
{

public function queryAll()
{
    try {

        $patients = Paciente::paginate(10);

        return response()->json([
            'data' => $patients->items(),
            'current_page' => $patients->currentPage(),
            'last_page' => $patients->lastPage(),
            'total' => $patients->total(),
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
  public function import(Request $request)
{
    try {

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        $file = $request->file('file');

        \Log::info('Iniciando importação Excel', [
            'file' => $file->getClientOriginalName()
        ]);

        $rows = Excel::toArray([], $file);

        if (empty($rows) || empty($rows[0])) {
            return response()->json([
                'message' => 'Planilha vazia'
            ], 400);
        }

        $sheet = $rows[0];

        // remove linhas completamente vazias
        $sheet = array_filter($sheet, fn($row) => is_array($row) && count(array_filter($row)) > 0);

        // detecta header automaticamente (primeira linha)
        $header = array_shift($sheet);

        $importados = 0;
        $erros = [];

        foreach ($sheet as $index => $row) {

            try {

                if (!is_array($row)) {
                    $erros[] = [
                        'linha' => $index + 2,
                        'erro' => 'Linha inválida (não é array)'
                    ];
                    continue;
                }

                // normaliza colunas
                $row = array_map(fn($v) => is_string($v) ? trim($v) : $v, $row);

                $codPaciente = $row[1] ?? null;

                if (!$codPaciente) {
                    $erros[] = [
                        'linha' => $index + 2,
                        'erro' => 'Código do paciente vazio'
                    ];
                    continue;
                }

                Paciente::updateOrCreate(
                    ['cod_paciente' => $codPaciente],
                    [
                        'nome' => $row[2] ?? null,
                        'cpf' => $row[3] ?? null,
                        'rg' => $row[4] ?? null,
                        'sexo' => $row[5] ?? null,
                    ]
                );

                $importados++;

            } catch (\Throwable $e) {

                $erros[] = [
                    'linha' => $index + 2,
                    'erro' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Importação concluída',
            'importados' => $importados,
            'erros' => $erros,
            'total_erros' => count($erros)
        ]);

    } catch (\Throwable $e) {

        \Log::error('ERRO IMPORTAÇÃO EXCEL', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
        ]);

        return response()->json([
            'message' => 'Erro ao importar planilha',
            'error' => $e->getMessage()
        ], 500);
    }
}
}