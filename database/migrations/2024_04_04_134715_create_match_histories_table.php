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
        Schema::create('match_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('match_id');
            $table->foreign('match_id')->references('id')->on('match_information');
            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id')->references('id')->on('team');
            $table->unsignedInteger('tournament_id');
            $table->foreign('tournament_id')->references('id')->on('tournaments');   
            $table->unsignedBigInteger('sticker_player_id')->nullable();
            $table->foreign('sticker_player_id')->references('id')->on('players');
            $table->unsignedBigInteger('nonsticker_player_id')->nullable();
            $table->foreign('nonsticker_player_id')->references('id')->on('players');
            $table->unsignedBigInteger('bowler_id')->nullable();
            $table->foreign('bowler_id')->references('id')->on('players');  
            $table->Integer('team_1_total_run')->default(0);
            $table->integer('team_1_total_wickets')->default(0);
            $table->integer('team_1_total_over')->default(0);
            $table->integer('team_1_extra_run')->default(0);
            $table->Integer('team_2_total_run')->default(0);
            $table->integer('team_2_total_wickets')->default(0);
            $table->integer('team_2_total_over')->default(0); 
            $table->integer('team_2_extra_run')->default(0);
            $table->decimal('runing_over', 10, 1);      
            $table->unsignedBigInteger('match_over_id');
            $table->foreign('match_over_id')->references('id')->on('match_overs');
            $table->integer('batsman_runs')->default(0);
            $table->integer('batsman_balls')->default(0);
            $table->integer('sixers')->default(0);
            $table->integer('fours')->default(0);
            $table->string('type_out')->nullable();
            $table->unsignedBigInteger('out_by_player_id')->nullable();
            $table->foreign('out_by_player_id')->references('id')->on('players');
            $table->unsignedBigInteger('out_by_bowler_id')->nullable();
            $table->foreign('out_by_bowler_id')->references('id')->on('players');
            $table->decimal('overs', 10, 1);      
            $table->integer('bowler_runs')->default(0);
            $table->integer('maiden_over')->default(0);
            $table->integer('wickets')->default(0);
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
        Schema::dropIfExists('match_histories');
    }
};
