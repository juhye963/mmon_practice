<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMallProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mall_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('filename')->default('no image');
            $table->integer('price')->unsigned();
            $table->integer('discount')->unsigned();
            // 공식문서에서 constraint 와 decimal 검색 (여기에 CHECK 제약조건 걸 방법 찾기)
            $table->unsignedMediumInteger('amount');

            $table->integer('seller_id')->unsigned();
            $table->integer('brand_id')->unsigned();
            $table->integer('category_id')->unsigned();
            $table->timestamps();

            $table->foreign('seller_id')->references('id')->on('mall_sellers')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('brand_id')->references('id')->on('mall_brands')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('mall_categories')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        //DB::statement('ALTER TABLE mall_products ADD CONSTRAINT chk_discount CHECK (discount <= 1.0000);');
        //mysql 은 check 제약조건 걸리지 않는다함(그래도 unsigned 는 잘 먹힘) -> 알고보니 0값으로 들어가고 insert 자체는 됨
        //workbench로 실험했을때는 1 이상의 값도 들어갔지만 실제로 웹사이트에서 넣어보니 위의 제약조건 지키고있음

    }

    //공식문서 migration 사용가능한 컬럼 타입들 참고

   /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mall_products');
    }
}
