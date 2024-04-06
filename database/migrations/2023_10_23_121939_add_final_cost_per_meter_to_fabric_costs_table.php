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
        Schema::table('fabric_costs', function (Blueprint $table) {
            $table->double('final_cost_per_meter', 15, 8)->nullable()->after('user_id');
            $table->double('final_cost_per_piece', 15, 8)->nullable()->after('final_cost_per_meter');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fabric_costs', function (Blueprint $table) {
            $table->double('final_cost_per_meter', 15, 8)->nullable();
            $table->double('final_cost_per_piece', 15, 8)->nullable();

        });
    }
};
