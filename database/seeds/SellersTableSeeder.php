<?php

use Illuminate\Database\Seeder;

class SellersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = App\Brand::all();

        $brands->each(function ($brand) {
            $brand->sellers()->createMany(
                factory(App\Seller::class, 5)->make()->toArray()
            );
        });
        //each 메소드는 컬렉션의 아이템을 반복적으로 처리하여 콜백에 각 아이템을 전달합니다.
        //save에 두번째 인자 들어가면 안됨(숫자)
        //save() must be an instance of Illuminate\Database\Eloquent\Model, instance of Illuminate\Database\Eloquent\Collection given,

        //컬렉션을 PHP 배열로 변환합니다. 컬렉션의 값이 Eloquent 라면, 이 모델 또한 배열로 변환
        //createMany 는 배열값 필요로함

    }
}
