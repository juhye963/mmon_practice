<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMallBrandProductsDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_brand_products_discount', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->unsigned();
            $table->integer('from_price')->unsigned();
            $table->tinyInteger('discount_percentage');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('mall_brands')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mall_brand_products_discount');
    }
}
