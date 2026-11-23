<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; //Agregado
use App\Models\Producto; //Agregado
use App\Http\Resources\ProductoResource; //Agregado

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $productos = Producto::orderBy('id','desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => ProductoResource::collection($productos),
            'pagination' => [
                'total' =>  $productos->total(),
                'per_page' =>  $productos->perPage(),
                'current_page' =>  $productos->currentPage(),
                'last_page' =>  $productos->lastPage(),
            ]
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Mapear la inserccion de datos en la BD a traves de una API
        $validados = $request->validate([
            // 'codigo_barra' => 'required|string|unique:productos,codigo_barra|max:30',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
            // 'categoria_id' => 'required|exists:categoria,id',
        ]);

        $producto = Producto::create($validados);
        return response()->json([
            'status' => 'created',
            'message' => 'Se creo el articulo en el inventario exitosamente.',
            'data' => new ProductoResource($producto)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /**
         * Busquedas por Id. Es un metodo GET, se instancia la clase de producto
         */
        $producto = Producto::find($id);
        if(!$producto){
            return response()->json([
                'status' => 'error',
                'message' => "El producto ID #{$id} no existe el la Ferreteria",
            ] ,404);
        }
        return response()->json([
            'status' => 'success',
            'data' => new ProductoResource($producto),
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Actualizar
        $producto = Producto::find($id);
        if(!$producto){
            return response()->json([
                'status'    => 'error',
                'message'   => "Producto no encontrado para actualizar",
            ], 404);
        }
        $validados = $request->validate([
            'nombre'        => 'required|string|max:150',
            'descripcion'   =>  'required|string|max:200', //Se agrego este, ya que sino no cambia descripcion.
            'precio'        => 'required|numeric|min:0.01',
            'stock'        => 'required|integer|min:0',
        ]);
        $producto->update($validados);
        return response()->json([
            'status'    => 'updated',
            'message'   => 'Producto actualizado correctamente.',
            'data'      => new ProductoResource($producto)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //DELETE
        $producto = Producto::find($id);
        if(!$producto) {
            return response()->json([
                'status' => 'error',
                'message' => 'El producto a eliminar no existe en bodega',
            ], 404);
        }
        $producto->delete();
        return response()->json([
            'status' => 'deleted',
            'message' => 'Producto dado de baja exitosamente.',
        ], 200);
    }
}
