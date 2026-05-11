<?php

// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\ProfilePermission;

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
        return Profile::with('permissions')->where(['id' => $id])->get();
    }

    public function update(Request $request, $id)
    {
        $profile_permissons = ProfilePermission::where(['profile_id' => $id])->delete();
        $permissions = Permission::whereIn('name',$request['permissions'])->get();
        $cont = 0;
       
        foreach($request['permissions'] as $perm) {
            $profile_permissons = ProfilePermission::create([
                'profile_id' => $id,
                'permission_id' => $permissions[$cont]['id'],
            ]);

            $cont++;
        }
        return response()->json($profile_permissons);
    }

    public function destroy($id)
    {
        $profile = Profile::findOrFail($id);
        $profile->update(['active' => false]);

        return response()->json(['message' => 'Perfil desativado']);
    }
}