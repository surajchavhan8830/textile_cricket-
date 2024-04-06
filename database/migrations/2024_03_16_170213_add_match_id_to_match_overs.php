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
        Schema::table('match_overs', function (Blueprint $table) {
            $table->unsignedBigInteger('match_ids')->after('id')->nullable();
            $table->foreign('match_ids')->references('id')->on('match_information');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('match_overs', function (Blueprint $table) {
            //
        });
    }
};
