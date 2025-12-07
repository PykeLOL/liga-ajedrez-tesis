<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanesEntrenamientoHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planes_entrenamiento_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_entrenamiento_id')->constrained('planes_entrenamiento')->cascadeOnDelete();
            $table->foreignId('dia_semana_id')->constrained('dias_semana')->cascadeOnDelete();
            $table->time('hora_inicio');
            $table->time('hora_fin');
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
        Schema::dropIfExists('planes_entrenamiento_horarios');
    }
}
