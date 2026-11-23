<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class RecursosHumanosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return 
        [
            'id'                => $this->id,
            'identidad'         => $this->identidad,
            'nombre'            => $this->nombre,
            'apellido'          => $this->apellido,
            'cargo'             => $this->cargo,
            'salario'           => $this->salario,
            'codigo_empleado'   => $this->codigo_empleado,
            'fecha_ingreso'    => $this->created_at ? $this->created_at->format('d/m/Y h:i A') : null,
        ];
    }
}
