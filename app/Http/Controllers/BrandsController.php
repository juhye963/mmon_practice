<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

//use Illuminate\Database\Eloquent\Model as Model;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('brands.select', ['brands' => $brands]);
        //gettype() 하면 object 반환
    }

    public function insertManyBrands() {

        $brands_name_suffix = ['Apple', 'Google', 'Coca-Cola', 'Microsoft', 'Toyota', 'IBM', 'Samsung', 'Amazon', 'Mercedes-Benz', 'GE'];
        $brands_name_prefix = ['BMW', 'McDonald\'s', 'Disney', 'Intel', 'Facebook', 'Cisco', 'Oracle', 'Nike', 'Louis Vuitton', 'H&M'];

        //dd(Arr::random($brands_name_prefix).' & '.Arr::random($brands_name_suffix));

        for ($i = 0; $i < 100; $i++) {
            $random_brand_name = Arr::random($brands_name_prefix).' & '.Arr::random($brands_name_suffix);
            if (Brand::where('name', '=', $random_brand_name)->first() != null) {
                $i--;
                continue;
            }
            Brand::create([
                'name' => $random_brand_name
            ]);
        }

        //재귀함수는 무한루프 돌 수 있으니 조심해서 써야함
        //너무 많이 반복했을시 break 빠져나오는 조건
        /*function makeRandomBrandName() {
        }*/

        return $i;
    }
}
