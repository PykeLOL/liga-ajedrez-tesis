<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrenamientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrenamientos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('plan_entrenamiento_id')->nullable()->constrained('planes_entrenamiento')->nullOnDelete();
            $table->foreignId('club_id')->constrained('clubes')->cascadeOnDelete();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->foreignId('genero_id')->constrained('generos')->cascadeOnDelete();
            $table->foreignId('entrenador_id')->constrained('entrenadores')->cascadeOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('ubicacion')->nullable();
            $table->string('url_mapa')->nullable();
            $table->foreignId('tipo_entrenamiento_id')->nullable()->constrained('tipos_entrenamiento')->cascadeOnDelete();
            $table->foreignId('evento_id')->nullable()->constrained('eventos')->nullOnDelete();
            $table->foreignId('estado_entrenamiento_id')->constrained('estados_entrenamiento');
            $table->boolean('generado_automaticamente')->default(false);

            $table->unique([
                'plan_entrenamiento_id',
                'fecha',
                'hora_inicio',
                'hora_fin'
            ]);

            $table->index([
                'plan_entrenamiento_id',
                'fecha'
            ]);

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
        Schema::dropIfExists('entrenamientos');
    }
}
