<?php

// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Permission;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return Profile::withCount('users')
        ->with('permissions')
        ->get();
    }

    public function store(Request $request)
    {
        $profile = Profile::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $permissions = Permission::whereIn('name', $request->permissions)->pluck('id');

        $profile->permissions()->sync($permissions);

        return response()->json($profile->load('permissions'));
    }

    public function show($id)
    {
        return Profile::with('permissions')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $profile = Profile::findOrFail($id);

        $profile->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $permissions = Permission::whereIn('name', $request->permissions)->pluck('id');

        $profile->permissions()->sync($permissions);

        return response()->json($profile->load('permissions'));
    }

    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $profile->update(['active' => false]);

        return response()->json(['message' => 'Perfil desativado']);
    }
}