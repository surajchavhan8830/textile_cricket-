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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tournament_name', 100);
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->string('location',100);
            $table->unsignedBigInteger('type_of_tournament_id');
            $table->foreign('type_of_tournament_id')->references('id')->on('tournament_type');
            $table->text('logo')->nullable();
            $table->text('description');
            $table->dateTime('strat_date');
            $table->dateTime('end_date');
            $table->dateTime('due_date');
            $table->text('address');
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
        Schema::dropIfExists('tournaments');
    }
};
