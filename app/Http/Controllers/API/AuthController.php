<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;    
//Librerias Agregadas
use Illuminate\Http\JsonResponse; //Agregado
use Illuminate\Support\Facades\Auth;//Agregado

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
        'email'     => 'required|email',
        'password'     => 'required|string',
    ]);
    if(!Auth::attempt($request->only('email','password')))
    {
        return response()->json([
            'status'    => 'error',
            'message'    => 'Credenciales invalidas de la Ferreteria.',
        ], 401);
    }
    $usuario = Auth::user();
    $token = $usuario->createToken('DispositivoFerreteria')->plainTextToken;
    return response()->json([
        'status'         => 'success',
        'mensaje'        => 'Bienvenido a la ferreteria',
        'token'          => $token,
        'usuario'        => [
            'id'    =>  $usuario->id,
            'name'  =>  $usuario->name,
            'email' =>  $usuario->email
        ]
    ], 200);
    }
}
