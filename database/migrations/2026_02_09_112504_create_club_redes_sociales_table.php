<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubRedesSocialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('club_redes_sociales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('club_id');
            $table->unsignedBigInteger('red_social_id');
            $table->unique(['club_id', 'red_social_id']);
            $table->integer('orden')->nullable();
            $table->text('url');

            $table->foreign('club_id')->references('id')->on('clubes')->onDelete('cascade');
            $table->foreign('red_social_id')->references('id')->on('redes_sociales')->onDelete('cascade');
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
        Schema::dropIfExists('club_redes_sociales');
    }
}
