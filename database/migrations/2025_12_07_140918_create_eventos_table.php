<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('liga_id')->constrained('ligas');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->foreignId('tipo_evento_id')->constrained('tipos_evento');
            $table->string('lugar')->nullable();
            $table->string('direccion')->nullable();
            $table->text('url_mapa')->nullable();
            $table->date('fecha_inicio');
            $table->time('hora_inicio')->nullable();
            $table->date('fecha_fin');
            $table->foreignId('estado_evento_id')->constrained('estados_evento');
            $table->boolean('de_pago')->default(false);
            $table->string('valor_rango')->nullable();
            $table->string('organizador_nombre')->nullable();
            $table->string('organizador_contacto')->nullable();
            $table->boolean('publicado')->default(false);
            $table->boolean('es_oficial')->default(true);
            $table->integer('max_participantes')->nullable();
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
        Schema::dropIfExists('eventos');
    }
}
