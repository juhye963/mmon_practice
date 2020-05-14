<?php

namespace App\Http\Controllers;

use App\Brand;
use App\BrandProductDiscount;
use App\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use function MongoDB\BSON\toJSON;

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


        $brands_name_prefix = ['BMW', 'McDonald\'s', 'Disney', 'Intel', 'Facebook', 'Cisco', 'Oracle', 'Nike', 'Louis Vuitton', 'H&M'];
        $brands_name_middle = ['Honda', 'SAP', 'Pepsi', 'Gillette', 'American Express', 'IKEA', 'Zara', 'Pampers', 'UPS', 'Budweiser'];
        $brands_name_suffix = ['Apple', 'Google', 'Coca-Cola', 'Microsoft', 'Toyota', 'IBM', 'Samsung', 'Amazon', 'Mercedes-Benz', 'GE'];

        $brands_data_set = [];

        for ($i = 0; $i < 200; $i++) {
            $random_brand_name = Arr::random($brands_name_prefix)
                . ' & ' . Arr::random($brands_name_middle)
                . ' & ' . Arr::random($brands_name_suffix);
            if (in_array($random_brand_name, $brands_data_set)) {
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
