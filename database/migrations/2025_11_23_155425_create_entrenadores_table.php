<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrenadoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrenadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubes')->onDelete('cascade');
            $table->date('fecha_nacimiento');
            $table->foreignId('genero_id')->constrained('generos')->onDelete('restrict');
            $table->foreignId('nacionalidad_id')->constrained('nacionalidades')->onDelete('restrict');
            $table->integer('experiencia_anios')->default(0);
            $table->string('especialidad')->nullable();
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
        Schema::dropIfExists('entrenadores');
    }
}
