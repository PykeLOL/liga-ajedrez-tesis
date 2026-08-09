<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposNotificacionesTable extends Migration
{
    public function up()
    {
        Schema::create('tipos_notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('icono')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipos_notificaciones');
    }
}
