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
            $table->increments('id');
            $table->unsignedBigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');
            $table->string('tournament_name', 100);
            $table->string('location', 50);
            $table->unsignedBigInteger('tournament_type_id');
            $table->foreign('tournament_type_id')->references('id')->on('tournament_type');
            $table->text('logo')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('strat_date');
            $table->dateTime('end_date');
            $table->dateTime('due_date');
            $table->text('address')->nullable();
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
