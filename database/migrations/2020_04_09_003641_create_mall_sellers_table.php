<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMallSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_sellers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->integer('brand_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('mall_brands')
                ->onUpdate('cascade')->onDelete('cascade');
            //브랜드.id 열이 변경 or 삭제될때 cascade
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mall_sellers');
    }
}
