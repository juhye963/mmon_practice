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

        $brands_data_set = [];

        for ($i = 0; $i < 200; $i++) {
            $random_brand_name = Arr::random($brands_name_prefix).' & '.Arr::random($brands_name_suffix);
            if (Brand::where('name', '=', $random_brand_name)->first() != null) {
                $i--;
                continue;
            }
            $brands_data_set[$i] = [
                'name' => $random_brand_name
            ];
        }

        Brand::insert($brands_data_set);

        return $i . "개의 브랜드 입력";
    }
}
