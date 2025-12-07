<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventoPosicionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evento_posiciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_categoria_id')->constrained('evento_categorias')->onDelete('cascade');
            $table->foreignId('deportista_id')->constrained('deportistas');
            $table->integer('puesto');
            $table->decimal('puntos', 8, 2)->default(0);
            $table->decimal('desempate', 8, 2)->nullable();
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
        Schema::dropIfExists('evento_posiciones');
    }
}
