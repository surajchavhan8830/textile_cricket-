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
        Schema::create('wefts', function (Blueprint $table) {
            $table->increments('id', true);
            $table->integer('fabric_cost_id');
            $table->integer('yarn_id');
            $table->integer('final_ppi');
            $table->float('weight');
            $table->float('rate');
            $table->float('amount');
            $table->float('denier');
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
        Schema::dropIfExists('wefts');
    }
};
