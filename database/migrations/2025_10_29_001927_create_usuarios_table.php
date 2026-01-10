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
            $table->foreignId('tipo_identificacion_id')->constrained('tipos_identificacion')->onDelete('restrict');
            $table->text('numero_identificacion')->nullable(false);
            $table->text('email')->unique()->nullable(false);
            $table->text('telefono')->nullable(true);
            $table->string('contraseña');
            $table->text('imagen_path')->nullable();
            $table->boolean('estado')->default(true);
            $table->foreignId('rol_id')->nullable()->constrained('roles')->onUpdate('cascade')->onDelete('set null');
            $table->text('google_id')->nullable();
            $table->text('google_email')->nullable();
            $table->text('google_token')->nullable();
            $table->text('google_refresh')->nullable();
            $table->timestamp('google_token_exp')->nullable();
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
