<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('liga_id')->nullable();
            $table->string('nombre');
            $table->string('ubicacion')->nullable();
            $table->unsignedBigInteger('presidente_id')->nullable();
            $table->text('contacto')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();

            $table->foreign('liga_id')->references('id')->on('ligas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clubes');
    }
}
