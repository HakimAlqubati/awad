<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitProductIdToOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
  
           $table->unsignedBigInteger('product_unit_id');
            $table->foreign('product_unit_id')->references('product_id')->on('unit_prices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['product_unit_id']);
            $table->dropColumn(['product_unit_id']);
        });
    }
}
