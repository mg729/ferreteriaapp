<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * id
     * identidad
     * nombre
     * Apellido
     * cargo
     * salario
     * codigo_empleado
     * fecha ingreso
     * 
     */
    public function up(): void
    {
        if (Schema::hasTable('empleados')){
            return;
        }
        /**
         *              Aca se saca el nombre
         *                  |
         *                  v    */
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('identidad', 13)->unique();
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('cargo');
            $table->decimal('salario', 10, 2);
            $table->string('codigo_empleado')->nullable()->unique();
            $table->date('fecha_ingreso')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
