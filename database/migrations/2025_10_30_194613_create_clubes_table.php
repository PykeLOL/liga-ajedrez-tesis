<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('liga_id')->nullable();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->text('direccion')->nullable();
            $table->text('url_mapa')->nullable();
            $table->unsignedBigInteger('presidente_id')->nullable();
            $table->text('contacto')->nullable();
            $table->string('logo')->nullable();
            $table->string('documento_path')->nullable();
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->timestamps();

            $table->foreign('liga_id')->references('id')->on('ligas')->onDelete('set null');
            $table->foreign('presidente_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clubes');
    }
}
