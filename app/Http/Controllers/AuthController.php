<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{

    public function queryAll()
    {
        try {
            $users = User::select(
                'id',
                'name',
                'usuario',
                'email',
                'telefone',
                'setor',
                'perfil',
                'status'
            )->get();

            return response()->json([
                'data' => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao buscar usuários',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        return response()->json($user);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('usuario', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return $this->respondWithToken($token);
    }


    public function register(Request $request)
{
    // ✅ validação
    $request->validate([
        'name' => 'required|string|max:255',
        'usuario' => 'required|string|max:100|unique:users,usuario',
        'email' => 'nullable|email|unique:users,email',
        'password' => 'required|string|min:6',
    ]);

    // ✅ criação do usuário
    $user = User::create([
        'name' => $request->name,
        'usuario' => $request->usuario,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'profile_id' => $request->profile_id
    ]);

    // 🔐 opcional: já logar e gerar token
    $token = auth('api')->login($user);

    return response()->json([
        'message' => 'Usuário criado com sucesso',
        'user' => $user,
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60
    ], 201);
}

public function update(Request $request, $id)
{
    try {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        // ✅ validação
        $request->validate([
            'name' => 'required|string|max:255',
            'usuario' => 'required|string|max:100|unique:users,usuario,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
            'telefone' => 'nullable|string',
            'setor' => 'nullable|string',
            'perfil' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        // ✅ atualiza campos
        $user->name = $request->name;
        $user->usuario = $request->usuario;
        $user->email = $request->email;
        $user->telefone = $request->telefone;
        $user->setor = $request->setor;
        $user->perfil = $request->perfil;

        // 🔐 atualiza senha somente se enviada
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Usuário atualizado com sucesso',
            'user' => $user
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro ao atualizar usuário',
            'error' => $e->getMessage()
        ], 500);
    }
}

   public function me()
{
    $user = User::with('profile.permissions')->find(auth()->id());

    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'status' => $user->status,
        'profile' => [
            'id' => $user->profile?->id,
            'name' => $user->profile?->name,
            'permissions' => $user->profile?->permissions?->pluck('name') ?? []
        ]
    ]);
}

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout realizado']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}