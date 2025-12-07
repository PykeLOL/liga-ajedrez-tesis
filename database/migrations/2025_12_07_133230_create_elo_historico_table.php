<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEloHistoricoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elo_historico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deportista_id');
            $table->string('periodo');
            $table->integer('clasico_elo')->nullable();
            $table->integer('clasico_juegos')->nullable();
            $table->integer('rapido_elo')->nullable();
            $table->integer('rapido_juegos')->nullable();
            $table->integer('blitz_elo')->nullable();
            $table->integer('blitz_juegos')->nullable();
            $table->date('fecha')->nullable();
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
        Schema::dropIfExists('elo_historico');
    }
}
