<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionesEntrenadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluaciones_entrenador', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->cascadeOnDelete();
            $table->foreignId('deportista_id')->constrained('deportistas')->cascadeOnDelete();
            $table->foreignId('entrenador_id')->constrained('entrenadores')->cascadeOnDelete();
            $table->integer('rendimiento'); // 1–5
            $table->text('comentarios')->nullable();
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
        Schema::dropIfExists('evaluaciones_entrenador');
    }
}
