# Tarea: RRHH
**Nota:** Al final utilice la tabla 'empleados' que se encuentra en la BD de ferreteria. Si gustan hacerlo de otra forma pueden seguir de guia este archivo. 

Las columnas las elegi, pero no es necesario seguirlo como tal, el ingeniero no dijo nada... creo.

Campos:
1. id - AUTOINCREMENT
2. identidad - string
3. nombre - string
4. apellido - string
5. cargo - string
6. salario - float
7. codigo_empleado - string
8. fecha_ingreso - date (NOTA: Este lo ignore basicamente pq tenia otros campos iguales. No lo quise quitar)


## Comandos
```bash
php artisan make:model Empleado
php artisan make:resource EmpleadoResource
php artisan make:controller API/RecursosHumanosController --api
```

## Archivos
### Modelo: app > Models > Empleado.php
>Este modelo ya viene enlazado a la tabla empleados.
```php
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
```
### Resource: app > Http > Resources > EmpleadoResource.php
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpleadoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'codigo_empleado' => $this->codigo_empleado,
            'identidad'       => $this->identidad,
            'nombre'          => $this->nombre,
            'apellido'        => $this->apellido,
            'nombre_completo' => $this->nombre . ' ' . $this->apellido,
            'cargo'           => $this->cargo,
            'salario'         => (float) $this->salario,
            'salario_hnl'     => 'L. ' . number_format($this->salario, 2),
            'fecha_ingreso'  => $this->created_at ? $this->created_at->format('d/m/Y h:i A') : null,
        ];
    }
}
```


### Controlador: app > Http > Controllers > API > RecursosHumanosController.php
```php
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
```

### Rutas: routes > api.php

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductoController; //Se añade esta ruta
use App\Http\Controllers\API\RecursosHumanosController; //Se agrega esta ruta


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('productos', ProductoController::class); //Productos
Route::apiResource('empleados', RecursosHumanosController::class); //RRHH

```
> Se verifica que las rutas esten utilizando: **php artisan route:list --path=api**

## POSTMAN
* GET       http://127.0.0.1:8000/api/empleados
* POST      http://127.0.0.1:8000/api/empleados
* GET       http://127.0.0.1:8000/api/empleados/1
* PUT       http://127.0.0.1:8000/api/empleados/1
* DELETE    http://127.0.0.1:8000/api/empleados/1

> POST y PUT json
```json
{
    "identidad": "0801199912345",
    "nombre": "Carlos",
    "apellido": "Mejia",
    "cargo": "Contador",
    "salario": 18500.00,
    "codigo_empleado": "EMP-001"
}
```

# Notas
* Encender el servidor, sino no funcionara **php artisan serve**
* Revisar si se usan las librerias en cada archivo.
* Noseee... lee despacio. Esto enreda mucho.


