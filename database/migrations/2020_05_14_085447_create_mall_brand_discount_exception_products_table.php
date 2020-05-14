<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMallBrandDiscountExceptionProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_brand_discount_exception_products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_discount_id')->unsigned();
            $table->integer('product_id')->unsigned();

            $table->foreign('brand_discount_id')->references('id')->on('mall_brand_products_discount')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('product_id')->references('id')->on('mall_products')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mall_brand_discount_exception_products');
    }
}
