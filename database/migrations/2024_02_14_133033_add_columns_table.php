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
        // Schema::table('match_information', function (Blueprint $table) {
        //     $table->unsignedBigInteger('match_status');
        //     $table->foreign('match_status')->references('id')->on('sechedule');
        //     $table->tinyInteger('group_id', 2);
        //     $table->string('toss', 100);
        //     $table->string('venue', 100);
        // });

        Schema::table('match_information', function (Blueprint $table) {
            $table->bigInteger('match_status')->unsigned()->nullable();
            $table->foreign('match_status')->references('id')->on('sechedule')->onDelete('cascade');
            $table->tinyInteger('group_id')->unsigned()->nullable(false);
            $table->string('toss', 100)->nullable(false);
            $table->string('venue', 100)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('match_information', function (Blueprint $table) {
            //
        });
    }
};
