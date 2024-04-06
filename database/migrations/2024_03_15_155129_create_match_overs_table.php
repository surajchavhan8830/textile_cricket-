<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('match_overs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sticker_player_id');
            $table->foreign('sticker_player_id')->references('id')->on('players');
            $table->unsignedBigInteger('nonsticker_player_id');
            $table->foreign('nonsticker_player_id')->references('id')->on('players');
            $table->integer('run')->nullable();
            $table->string('bowl_type', 10);
            $table->unsignedBigInteger('bowler_player_id');
            $table->foreign('bowler_player_id')->references('id')->on('players');
            $table->integer('over_number')->nullable();
            $table->integer('bowl_number')->nullable();
            $table->string('out_type', 10)->nullable();
            $table->unsignedBigInteger('out_by_player_id');
            $table->foreign('out_by_player_id')->references('id')->on('players');
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
        Schema::dropIfExists('match_overs');
    }
};
