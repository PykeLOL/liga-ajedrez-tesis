<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partidas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('evento_categoria_id')->nullable()->constrained('evento_categorias')->nullOnDelete();
        $table->foreignId('deportista_blancas_id')->constrained('deportistas')->cascadeOnDelete();
        $table->foreignId('deportista_negras_id')->constrained('deportistas')->cascadeOnDelete();
        $table->foreignId('ganador_id')->nullable()->constrained('deportistas')->nullOnDelete();
        $table->foreignId('ritmo_id')->constrained('ritmos')->cascadeOnDelete();
        $table->foreignId('apertura_id')->nullable()->constrained('aperturas')->nullOnDelete();
        $table->date('fecha');
        $table->string('resultado'); // "1-0", "0-1", "½-½"
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
        Schema::dropIfExists('partidas');
    }
}
