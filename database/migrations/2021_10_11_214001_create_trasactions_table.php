<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrasactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trasactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->text('desc')->nullable();     
            $table->integer('active')->default(1);    

            $table->unsignedBigInteger('requseted_by');
            $table->foreign('requseted_by')->references('id')->on('users')->onDelete('cascade');


            $table->unsignedBigInteger('restricted_by');
            $table->foreign('restricted_by')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('order_state_id');
            $table->foreign('order_state_id')->references('id')->on('request_states')->onDelete('cascade');
            $table->morphs('transaction');



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
        Schema::dropIfExists('trasactions');
    }
}
