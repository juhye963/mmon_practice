<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class SellersController extends Controller
{
    public function create()
    {
        $brands = Brand::all();

        return view('sellers.create', ['brands' => $brands]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:mall_sellers,email', //unique 검사란??
            'password' => 'required|confirmed|min:6', //패스워드 confirm 하는 필드가 입력값중 있어야한다는 뜻
            'brand_id' => 'required|exists:mall_brands,id'
            //https://laravel.kr/docs/6.x/validation 참고
        ]);

        $seller = Seller::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'brand_id' => $request->input('brand_id')
        ]);
        // https://laravel.kr/docs/6.x/requests#%EC%9E%85%EB%A0%A5%EA%B0%92%20%EC%A1%B0%ED%9A%8C%ED%95%98%EA%B8%B0
    }

    public function brand_edit()
    {
        $brands = Brand::all();
        $seller = auth()->user();
        // config\auth.php 의 설정이 auth 부르면 App\Seller 모델 부른다는 뜻임

        $sellers_brand = $seller->brand()->first();

        return view('sellers.brand.edit')->with([
            'brand' => $sellers_brand,
            'brands' => $brands
        ]);
    }

    public function brand_update(Request $request)
    {
        $this->validate($request, [
            'brand_id' => 'required|exists:mall_brands,id'
        ]);

        $seller = auth()->user();
        $seller->brand_id = $request->input('brand_id');
        $seller->save();

        return redirect()->route('home');
    }
}
