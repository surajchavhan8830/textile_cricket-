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
        Schema::create('tournament_match', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('tournament_id');
            $table->foreign('tournament_id')->references('id')->on('tournaments');
            $table->dateTime('match_strat');
            $table->dateTime('end_date');
            $table->tinyInteger('active')->nullable()->default(0);
            $table->unsignedBigInteger('schedule_id');
            $table->foreign('schedule_id')->references('id')->on('sechedule');
            $table->integer('group_id')->nullable();
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
        Schema::dropIfExists('tournament_match');
    }
};
