<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request){
      $credentials = $request->validate([
        'email' => 'string|required|email',
        'password' => 'string|required'
      ]);

      if (auth()->attempt($credentials)) {
        $user = Auth::user();
        $user['token'] = $user->createToken('Passport App')->accesssToken;
        return response()->json([
            'user' = $user
        ], 200);
      }
      return response()->json([
        'message' = 'Credenciais estão invalidas'
      ], 402);
}
}





