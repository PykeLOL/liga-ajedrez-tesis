<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventoInscripcionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evento_inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('evento_categoria_id')->constrained('evento_categorias')->onDelete('cascade');
            $table->foreignId('deportista_id')->constrained('deportistas');
            $table->timestamp('fecha_inscripcion')->nullable();
            $table->boolean('pago')->default(false);
            $table->string('comprobante_path')->nullable();
            $table->decimal('valor_pagado', 12, 2)->nullable();
            $table->string('referencia_pago')->nullable();
            $table->foreignId('estado_inscripcion_id')->constrained('estados_inscripcion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evento_inscripciones');
    }
}
