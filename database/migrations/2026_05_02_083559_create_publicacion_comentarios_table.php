<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionComentariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicacion_comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publicacion_id')->constrained('publicaciones')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->text('comentario');
            $table->string('imagen_path')->nullable();
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
        Schema::dropIfExists('publicacion_comentarios');
    }
}
