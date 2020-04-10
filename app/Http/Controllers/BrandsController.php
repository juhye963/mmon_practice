<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Eloquent\Model as Model;
use App\Mall_Seller;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = DB::table('mall_brands')->get();

        //DB파사드에 select(쿼리)->all() 했더니
        // Error - Call to a member function all() on array

        return view('brands.select', ['brands' => $brands]);
        //gettype() 하면 object 반환(배열인가 객체인가?)
        //https://laravel.kr/docs/6.x/mail#%3Ccode%3Ewith%3C/code%3E%20%EB%A9%94%EC%86%8C%EB%93%9C%EB%A5%BC%20%EC%82%AC%EC%9A%A9%ED%95%98%EC%97%AC:
    }

    public function edit()
    {
        $brands = DB::table('mall_brands')->get();
        //$seller_name = auth()->user()->name;
        $current_seller = auth()->user()->id;
        //$current_brand = App\Mall_Seller::find($current_seller)->mall_brand()->get();

        /*$seller = new Mall_Seller;
        $current_brand = $seller->find($current_seller)->mall_brand()->get();*/
        //$current_brand = Mall_Seller::find(10)->mall_brand()->get();

        $seller = Mall_Seller::find($current_seller);
        $current_brand = $seller->mall_brand()->get();
        foreach ($current_brand as $brand) {
            $brand_id = $brand->id;
            $brand_name = $brand->name;
        }

        return view('brands.edit')->with([
            'brand_id' => $brand_id,
            'brand_name' => $brand_name,
            'brands' => $brands
        ]);
        //with()은 메서드체이닝 가능 = 여러변수 넘겨줘야하는 경우 씀
        //이 경우 여러 변수 넘겨줘야하니 with() 으로 바꾸기

        //return view('brands.update',['current_brand' => $current_brand]);
    }

    public function update(Request $request)
    {
        $current_seller = auth()->user()->id;
        $seller = Mall_Seller::find($current_seller);
        $seller->brand_id = $request->input('brand_id');
    }


}
