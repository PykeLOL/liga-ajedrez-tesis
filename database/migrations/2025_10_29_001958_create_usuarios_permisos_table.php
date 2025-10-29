<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosPermisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_permisos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                  ->constrained('usuarios')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreignId('permiso_id')
                  ->constrained('permisos')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unique(['usuario_id', 'permiso_id'], 'unq_usuario_permiso');
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
        Schema::dropIfExists('usuarios_permisos');
    }
}
