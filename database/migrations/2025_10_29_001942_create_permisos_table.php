<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->text('nombre')->unique();
            $table->text('descripcion')->nullable();

            $table->unsignedBigInteger('tipo_accion_id')->nullable();
            $table->unsignedBigInteger('modulo_id')->nullable();
            $table->timestamps();

            $table->foreign('tipo_accion_id')->references('id')->on('tipo_accion')->onDelete('set null');
            $table->foreign('modulo_id')->references('id')->on('modulos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permisos');
    }
}
