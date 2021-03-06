<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Seller;
use http\Env\Response;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class SellersController extends Controller
{
    public function create()
    {
        $brands = Brand::all();

        return view('sellers.create', ['brands' => $brands]);
    }

    public function emailDuplicateCheck(Request $request)
    {
        $check_message ='';
        $email = $request->input('email');

        $duplicate_email_check = Seller::where('email', '=', $email)->first();

        if ($duplicate_email_check instanceof Seller) {
            $check_message = '이미 존재하는 이메일입니다.';
        } elseif ($duplicate_email_check == null) {
            $check_message = '사용 가능한 이메일입니다.';
        }

        return response()->json([
            "message" => $check_message
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'seller_name' => 'required|max:255',
            'seller_email' => 'required|email|max:255|unique:mall_sellers,email',
            'password' => 'required|confirmed|min:6',
            'seller_brand_id' => 'required|exists:mall_brands,id'
        ]);

        $seller = Seller::create([
            'name' => $request->input('seller_name'),
            'email' => $request->input('seller_email'),
            'password' => bcrypt($request->input('password')),
            'brand_id' => $request->input('seller_brand_id')
        ]);

        $seller->save();

        return response()->json([]);
    }

    public function brand_edit()
    {
        $brands = Brand::all();
        $seller = auth()->user();

        $sellers_brand = $seller->brand;
        //메서드로 호출안하면 그 모델 자체를 줌
        //프로퍼티처럼 접근하면 이렇게됨
        //메서드일때 쿼리빌더 모델?
        //프로퍼티일때 모델 자체?

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

    public function insertManySellers() {

        //dd(date('Y-m-d H:i:s', mt_rand(1,time())));
        app('debugbar')->disable();
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit','512M');

        $sellers_data_set = [];

        for ($i = 0; $i < 1500; $i++) {

            $date = date('Y-m-d H:i:s', mt_rand(1,time()));

            $sellers_data_set[$i] = [
                'name' => Str::random(6),
                'email' => Str::random(5).'@example.com',
                'password' => bcrypt('password'),
                'brand_id' => Brand::all()->random()->id,
                'created_at' => $date,
                'updated_at' => $date
            ];
        }

        //dd($sellers_data_set);

        Seller::insert($sellers_data_set);

        return $i . "건의 데이터 입력";
    }
}
