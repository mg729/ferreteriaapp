<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;//Agregado
use Illuminate\Validation\Rule;//
use App\Models\Empleado; //Agregado
use App\Http\Resources\EmpleadoResource; //Agregado

class RecursosHumanosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //METODO GET
        $empleados = Empleado::orderBy('id', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => EmpleadoResource::collection($empleados),
            'pagination' => [
                'total'        => $empleados->total(),
                'per_page'     => $empleados->perPage(),
                'current_page' => $empleados->currentPage(),
                'last_page'    => $empleados->lastPage(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // POST /api/empleados
        $validados = $request->validate([
            'identidad'       => 'required|digits:13|unique:empleados,identidad',
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'cargo'           => 'required|string|max:100',
            'salario'         => 'required|numeric|min:0.01',
            'codigo_empleado' => 'required|string|max:20|unique:empleados,codigo_empleado',
        ]);

        $empleado = Empleado::create($validados);

        return response()->json([
            'status'  => 'created',
            'message' => 'Se registró el empleado correctamente',
            'data'    => new EmpleadoResource($empleado)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // GET /api/empleados/{id}
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'status'  => 'error',
                'message' => "El empleado ID #{$id} no existe en Recursos Humanos",
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => new EmpleadoResource($empleado),
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // PUT 
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Empleado no encontrado para actualizar',
            ], 404);
        }

        $validados = $request->validate([
            // ignore($empleado->id) evita que falle porque "se compara contra si mismo"
            'identidad'       => ['required', 'digits:13', Rule::unique('empleados', 'identidad')->ignore($empleado->id)],
            'nombre'          => 'required|string|max:100',
            'apellido'        => 'required|string|max:100',
            'cargo'           => 'required|string|max:100',
            'salario'         => 'required|numeric|min:0.01',
            'codigo_empleado' => ['required', 'string', 'max:20', Rule::unique('empleados', 'codigo_empleado')->ignore($empleado->id)],
        ]);

        $empleado->update($validados);

        return response()->json([
            'status'  => 'updated',
            'message' => 'Empleado actualizado correctamente.',
            'data'    => new EmpleadoResource($empleado)
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // DELETE
        $empleado = Empleado::find($id);

        if (!$empleado) {
            return response()->json([
                'status'  => 'error',
                'message' => 'El empleado a eliminar no existe',
            ], 404);
        }

        $empleado->delete();

        return response()->json([
            'status'  => 'deleted',
            'message' => 'Empleado dado de baja exitosamente.',
        ], 200);
    }
}
