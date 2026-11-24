<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductoController;
use App\Http\Controllers\API\RecursosHumanosController;
use App\Http\Controllers\API\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Publico: pide login al API
Route::post('/login', [AuthController::class, 'login']);
Route::apiResource('empleados', RecursosHumanosController::class)->names('api.empleados');

// Protegido con token
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('productos', ProductoController::class)->names('api.productos');
});