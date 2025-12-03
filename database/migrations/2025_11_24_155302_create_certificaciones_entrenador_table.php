<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificacionesEntrenadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificaciones_entrenador', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('entidad_id')->constrained('entidades_certificacion');
            $table->foreignId('entrenador_id')->constrained('entrenadores')->onDelete('cascade');
            $table->text('descripcion')->nullable();
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
        Schema::dropIfExists('certificaciones_entrenador');
    }
}
