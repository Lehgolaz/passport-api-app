<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    function login(Request $request)
    {
        //pegar o usuario e senha
        $credentials = $request->validate([
            'email' => 'string|required|email',
            'password' => 'string|required'
        ]);
        if ($validator->fails()){
            return response()->json([
                'message' => 'Dados de entrada inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }
        //verificar se ele existe no banco de dados
        //Auth::attempt($credentials)
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $user['token'] = $user->createToken('Passaport App')->accessToken;
            return response()->json([
                'user' => $user
            ], 200);
        }
        // em caso de erro
        return response()->json([
            'message' => 'Credenciais estão invalidas'
        ], 402);
    }

    function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout realizado com sucesso!',
        ]);
    }

    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return response()->json([
                'message'=> 'Error de validação',
                'errors' => $validator->errors()
            ], 422);
        };
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json([
            'message' => 'Usuario criado com sucesso',
            'user' => $user
        ], 201);
    }
}