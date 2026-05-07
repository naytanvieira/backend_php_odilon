<?php

namespace App\Http\Controllers;

use App\Models\SpreadsheetType;
use Illuminate\Http\Request;

class SpreadsheetTypeController extends Controller
{
    /* =========================
       LIST
    ========================= */
    public function index()
    {
        $types = SpreadsheetType::orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $types
        ]);
    }

    /* =========================
       SHOW
    ========================= */
    public function show($id)
    {
        $type = SpreadsheetType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $type
        ]);
    }

    /* =========================
       STORE
    ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'multiple_files' => 'required|boolean',
            'manual_time' => 'nullable|string|max:255',

            'nome_funcao_python' => 'nullable|string|max:255',

            'index_select' => 'nullable|integer',
        ]);

        $type = SpreadsheetType::create([
            'name' => $request->name,
            'multiple_files' => $request->multiple_files,
            'manual_time' => $request->manual_time,
            'nome_funcao_python' => $request->nome_funcao_python,
            'index_select' => $request->index_select,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo criado com sucesso',
            'data' => $type
        ], 201);
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        $type = SpreadsheetType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo não encontrado'
            ], 404);
        }

       $request->validate([
            'name' => 'required|string|max:255',
            'multiple_files' => 'required|boolean',
            'manual_time' => 'nullable|string|max:255',

            'nome_funcao_python' => 'nullable|string|max:255',

            'index_select' => 'nullable|integer',
        ]);

        $type->update([
            'name' => $request->name,
            'multiple_files' => $request->multiple_files,
            'manual_time' => $request->manual_time,
            'nome_funcao_python' => $request->nome_funcao_python,
            'index_select' => $request->index_select,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipo atualizado com sucesso',
            'data' => $type
        ]);
    }

    /* =========================
       DELETE
    ========================= */
    public function destroy($id)
    {
        $type = SpreadsheetType::find($id);

        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo não encontrado'
            ], 404);
        }

        $type->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo removido com sucesso'
        ]);
    }
}