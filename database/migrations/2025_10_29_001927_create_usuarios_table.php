<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->text('nombre')->nullable(false);
            $table->text('apellido')->nullable(false);
            $table->text('documento')->nullable(false);
            $table->text('email')->unique()->nullable(false);
            $table->text('telefono')->nullable(true);
            $table->text('contraseña')->nullable(false);
            $table->text('imagen_path')->nullable(true);
            $table->boolean('estado')->default(true);

            $table->foreignId('rol_id')
                  ->nullable()
                  ->constrained('roles')
                  ->onUpdate('cascade')
                  ->onDelete('set null');

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
        Schema::dropIfExists('usuarios');
    }
}
