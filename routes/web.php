<?php

use Illuminate\Support\Facades\Route;

Route::get('/hola', function () {
    return ('hola mundo programacion web');
});

Route::get('/hola2', function () {
    return view('prueba', [
        'empresa' => 'Ferretería La Bendición24'
    ]);

    
});

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EmpleadoController;

Route::resource('productos', ProductoController::class);
Route::resource('empleados', EmpleadoController::class);

//Debe de quedar asi:
Route::get('/', function () {
    return redirect()->route('productos.index');
});
