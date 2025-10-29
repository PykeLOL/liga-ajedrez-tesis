<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesPermisosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles_permisos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rol_id')
                  ->constrained('roles')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreignId('permiso_id')
                  ->constrained('permisos')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->unique(['rol_id', 'permiso_id'], 'unq_rol_permiso');
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
        Schema::dropIfExists('roles_permisos');
    }
}
