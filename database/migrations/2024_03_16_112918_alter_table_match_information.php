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
        Schema::table('match_information', function (Blueprint $table) {
            $table->unsignedBigInteger('sticker_player_id')->after('match')->nullable();
            $table->foreign('sticker_player_id')->references('id')->on('players');
            $table->unsignedBigInteger('nonsticker_player_id')->after('sticker_player_id')->nullable();
            $table->foreign('nonsticker_player_id')->references('id')->on('players');
            $table->unsignedBigInteger('bowler_id')->after('nonsticker_player_id')->nullable();
            $table->foreign('bowler_id')->references('id')->on('players');
            $table->Integer('team_1_total_run')->after('team_1')->default(0);
            $table->integer('team_1_total_wickets')->after('team_1_total_run')->default(0);
            $table->integer('team_1_total_over')->after('team_1_total_wickets')->default(0);
            $table->Integer('team_2_total_run')->after('team_2')->default(0);
            $table->integer('team_2_total_wickets')->after('team_2_total_run')->default(0);
            $table->integer('team_2_total_over')->after('team_2_total_wickets')->default(0);
        });
    }

    /** 
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
