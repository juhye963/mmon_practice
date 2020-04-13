<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Eloquent\Model as Model;
use App\Seller;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::all();

        return view('brands.select', ['brands' => $brands]);
        //gettype() 하면 object 반환(배열인가 객체인가?)
        //https://laravel.kr/docs/6.x/mail#%3Ccode%3Ewith%3C/code%3E%20%EB%A9%94%EC%86%8C%EB%93%9C%EB%A5%BC%20%EC%82%AC%EC%9A%A9%ED%95%98%EC%97%AC:
    }

    public function edit()
    {
        $brands = Brand::all();
        $current_seller_id = auth()->user()->id;

        $seller = Seller::find($current_seller_id);

        $current_brand = $seller->brand()->get();
        foreach ($current_brand as $brand) {
            $brand_id = $brand->id;
            $brand_name = $brand->name;
        }

        return view('brands.edit')->with([
            'brand_id' => $brand_id,
            'brand_name' => $brand_name,
            'brands' => $brands
        ]);

/*get()과 foreach 안쓰는 방법 찾아봤음 but [""]등의 필요없는 문자 나옴(정제해야함)
        $current_brand = $seller->mall_brand();
        return view('brands.edit')->with([
            'brand_id' => $current_brand->pluck('id'),
            'brand_name' => $current_brand->pluck('name'),
            'brands' => $brands
        ]);
https://laravel.kr/docs/6.x/queries#%EC%BB%AC%EB%9F%BC%20%EA%B0%92%EC%9D%98%20%EB%AA%A9%EB%A1%9D%20%EC%A1%B0%ED%9A%8C%ED%95%98%EA%B8%B0
참고
        */
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'brand_id' => 'required'
        ]);
        $current_seller_id = auth()->user()->id;
        $seller = Seller::find($current_seller_id);

        $seller->brand_id = $request->input('brand_id');
        $seller->save();

        return redirect()->route('home');
    }


}
