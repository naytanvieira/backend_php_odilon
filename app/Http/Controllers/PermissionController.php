<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /* =========================
        LIST ALL
    ========================= */
    public function index()
    {
        $permissions = Permission::orderBy('module')
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $permissions
        ]);
    }

    /* =========================
        STORE
    ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'label' => 'required',
            'module' => 'required',
        ]);

        $permission = Permission::create($request->all());

        return response()->json([
            'message' => 'Permissão criada com sucesso',
            'data' => $permission
        ], 201);
    }

    /* =========================
        SHOW
    ========================= */
    public function show($id)
    {
        $permission = Permission::findOrFail($id);

        return response()->json([
            'data' => $permission
        ]);
    }

    /* =========================
        UPDATE
    ========================= */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
            'label' => 'required',
            'module' => 'required',
        ]);

        $permission->update($request->all());

        return response()->json([
            'message' => 'Permissão atualizada com sucesso',
            'data' => $permission
        ]);
    }

    /* =========================
        DELETE
    ========================= */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'message' => 'Permissão removida com sucesso'
        ]);
    }
}