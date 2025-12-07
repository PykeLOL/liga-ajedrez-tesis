<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstadisticasDeportistaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estadisticas_deportista', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deportista_id');
            $table->integer('partidas_jugadas')->default(0);
            $table->integer('partidas_ganadas')->default(0);
            $table->integer('partidas_perdidas')->default(0);
            $table->integer('partidas_tablas')->default(0);
            $table->integer('rendimiento')->nullable(); // Performance rating
            $table->integer('mejor_elo_clasico_vencido')->nullable();
            $table->integer('mejor_elo_rapido_vencido')->nullable();
            $table->integer('mejor_elo_blitz_vencido')->nullable();
            $table->date('fecha_actualizacion')->nullable();
            $table->timestamps();
            $table->foreign('deportista_id')->references('id')->on('deportistas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estadisticas_deportista');
    }
}
