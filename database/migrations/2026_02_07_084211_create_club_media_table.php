<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained('clubes')->onDelete('cascade');
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
        Schema::dropIfExists('club_media');
    }
}
