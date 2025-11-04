<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeportistasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deportistas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_identificacion');
            $table->integer('elo_nacional')->nullable();
            $table->integer('elo_internacional')->nullable();
            $table->string('fide_id')->nullable();
            $table->date('fecha_nacimiento');
            $table->boolean('estado')->default(true);

            $table->foreignId('usuario_id')->unique()->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubes')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('genero_id')->constrained('generos')->onDelete('cascade');
            $table->foreignId('nacionalidad_id')->constrained('nacionalidades')->onDelete('cascade');
            $table->foreignId('tipo_identificacion_id')->constrained('tipos_identificacion')->onDelete('cascade');
            $table->foreignId('titulo_id')->nullable()->constrained('titulos')->onDelete('set null');

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
        Schema::dropIfExists('deportistas');
    }
}
