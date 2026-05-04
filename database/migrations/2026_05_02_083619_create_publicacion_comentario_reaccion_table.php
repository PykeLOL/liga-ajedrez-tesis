<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionComentarioReaccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicacion_comentario_reaccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publicacion_comentario_id')->constrained('publicacion_comentarios')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('reaccion_id')->constrained('reacciones')->onDelete('cascade');
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
        Schema::dropIfExists('publicacion_comentario_reaccion');
    }
}
