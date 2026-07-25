<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanEntrenamientoDeportistasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('planes_entrenamiento_deportistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_entrenamiento_id')->constrained('planes_entrenamiento')->cascadeOnDelete();
            $table->foreignId('deportista_id')->constrained('deportistas')->cascadeOnDelete();
            $table->boolean('estado')->default(true);
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
        Schema::dropIfExists('planes_entrenamiento_deportistas');
    }
}
