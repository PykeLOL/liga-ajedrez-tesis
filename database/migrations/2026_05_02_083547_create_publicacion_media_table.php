<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublicacionMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicacion_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publicacion_id')->constrained('publicaciones')->onDelete('cascade');
            $table->enum('tipo', ['imagen', 'video', 'url']);
            $table->string('path');
            $table->text('descripcion')->nullable();
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
        Schema::dropIfExists('publicacion_media');
    }
}
