<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntrenamientoGoogleEventsTable extends Migration
{
    public function up()
    {
        Schema::create('entrenamiento_google_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entrenamiento_id')->constrained('entrenamientos')->cascadeOnDelete();
            $table->string('google_event_id');
            $table->timestamps();

            $table->unique(['usuario_id', 'entrenamiento_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('entrenamiento_google_events');
    }
}
