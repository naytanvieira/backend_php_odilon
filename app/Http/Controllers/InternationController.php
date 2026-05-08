<?php

namespace App\Http\Controllers;

use App\Models\Internacoes;
use Illuminate\Http\Request;

class InternationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTAR INTERNACOES
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $internacoes = Internacoes::paginate(10);

        return response()->json([
            'data' => $internacoes->items(),
            'current_page' => $internacoes->currentPage(),
            'last_page' => $internacoes->lastPage(),
            'total' => $internacoes->total(),
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

        $internacao = Internacao::create($request->all());

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
        $internacao = Internacao::with('paciente')->findOrFail($id);

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
        $internacao = Internacao::findOrFail($id);

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
        $internacao = Internacao::findOrFail($id);

        $internacao->delete();

        return response()->json([
            'message' => 'Internação removida com sucesso'
        ]);
    }
}