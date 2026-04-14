<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventoCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evento_categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('ritmo_id')->nullable()->constrained('ritmos');
            $table->integer('cupo_maximo')->nullable();
            $table->decimal('costo_inscripcion', 12, 2)->nullable();
            $table->foreignId('genero_id')->constrained('generos');
            $table->integer('orden')->nullable();
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
        Schema::dropIfExists('evento_categorias');
    }
}
