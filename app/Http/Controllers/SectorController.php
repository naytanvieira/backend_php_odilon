<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    /* =========================
       LIST
    ========================= */
    public function index()
    {
        return response()->json(
            Sector::orderBy('id', 'desc')->get()
        );
    }

    /* =========================
       SHOW
    ========================= */
    public function show($id)
    {
        $sector = Sector::find($id);

        if (!$sector) {
            return response()->json([
                'message' => 'Setor não encontrado'
            ], 404);
        }

        return response()->json($sector);
    }

    /* =========================
       CREATE
    ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'status' => 'required|string',
        ]);

        $sector = Sector::create([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Setor criado com sucesso',
            'data' => $sector
        ], 201);
    }

    /* =========================
       UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        $sector = Sector::find($id);

        if (!$sector) {
            return response()->json([
                'message' => 'Setor não encontrado'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'status' => 'required|string',
        ]);

        $sector->update([
            'name' => $request->name,
            'code' => $request->code,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Setor atualizado com sucesso',
            'data' => $sector
        ]);
    }

    /* =========================
       DELETE
    ========================= */
    public function destroy($id)
    {
        $sector = Sector::find($id);

        if (!$sector) {
            return response()->json([
                'message' => 'Setor não encontrado'
            ], 404);
        }

        $sector->delete();

        return response()->json([
            'message' => 'Setor removido com sucesso'
        ]);
    }
}