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
        Schema::create('fabric_costs', function (Blueprint $table) {
            $table->increments('id', true);
            $table->string('fabric_name', 50);
            $table->integer('warp_yarn');
            $table->integer('weft_yarn',);
            $table->float('width');
            $table->float('final_ppi');
            $table->float('warp_wastage');
            $table->float('weft_wastage');
            $table->float('butta_cutting_cost');
            $table->float('additional_cost');
            $table->integer('fabric_category_id');
            $table->integer('user_id');
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
        Schema::dropIfExists('fabric_costs');
    }
};
