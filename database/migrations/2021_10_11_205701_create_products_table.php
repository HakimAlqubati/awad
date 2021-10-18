<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('desc')->nullable();
            $table->unsignedBigInteger('cat_id');
            $table->foreign('cat_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->string('code');
            $table->integer('active')->default(1);
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
      
        Schema::table('products', function ($table) {

            $table->dropForeign(['cat_id']);
            $table->dropIfExists('products');
        });
    }
}
