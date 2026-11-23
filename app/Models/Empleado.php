<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    //definiendo $fillable
    protected $fillable = [
        'identidad', 
        'nombre', 
        'apellido', 
        'cargo',
        'salario', 
        'codigo_empleado', 
        'fecha_ingreso',
    ];
}

