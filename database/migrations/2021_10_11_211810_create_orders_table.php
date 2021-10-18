<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
         
            $table->text('desc')->nullable();     
            $table->integer('active')->default(1); 

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('request_state_id');
            $table->foreign('request_state_id')->references('id')->on('request_states')->onDelete('cascade');

            $table->unsignedBigInteger('restricted_state_id');
            $table->foreign('restricted_state_id')->references('id')->on('request_states')->onDelete('cascade');


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
       
        Schema::table('orders', function ($table) {

            $table->dropForeign(['created_by']);
            $table->dropForeign(['request_state_id']);
            $table->dropForeign(['restricted_state_id']);

            $table->dropIfExists('orders');
        });
    }
}
