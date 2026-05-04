<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudesDeportistaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes_deportista', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes');
            $table->foreignId('club_id')->constrained('clubes');
            $table->foreignId('genero_id')->constrained('generos');
            $table->foreignId('nacionalidad_id')->constrained('nacionalidades');
            $table->string('fide_id')->nullable()->unique();
            $table->date('fecha_nacimiento');
            $table->text('documento_path');
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
        Schema::dropIfExists('solicitudes_deportista');
    }
}
