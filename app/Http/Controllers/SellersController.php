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
        //dd($email);

        $duplicate_email_check = Seller::where('email', '=', $email)->first();

        //dd($duplicate_email_check instanceof Seller);

        if ($duplicate_email_check instanceof Seller) {
            $check_message = '이미 존재하는 이메일입니다.';
        } elseif ($duplicate_email_check == null) {
            $check_message = '사용 가능한 이메일입니다.';
        }

        //dd($message);

        return response()->json([
            "message" => $check_message
        ]);

        /*return response()->json([
            "message" => "ok",
            "is_pass" => false,
        ]);*/
    }

    public function store(Request $request)
    {
        //$test = $request->input('name');
        //$test = $request->input('email');
        //$test = $request->input('brand_id');
        //dd($test);

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:mall_sellers,email', //unique 검사란??
            'password' => 'required|confirmed|min:6', //패스워드 confirm 하는 필드가 입력값중 있어야한다는 뜻
            'brand_id' => 'required|exists:mall_brands,id'
        ]);
        //유효성 검사를 통과하지 못할 경우, 예외-exception가 던져지고 적절한 오류 응답
        //전통적인 HTTP 요청의 경우, 리다이렉트 응답이 생성될 것이며 AJAX 요청에는 JSON 응답이 보내질 것입니다.

        try {
            $seller = Seller::create([
                'name' => $request->input('name'),
                'emai' => $request->input('email'),
                'password' => bcrypt($request->input('password')),
                'brand_id' => $request->input('brand_id')
            ]);
            $success_fail_message = '회원가입 완료';
        } catch (QueryException $queryException) {
            //$exception_message = $queryException->getMessage();
            $success_fail_message = '회원가입에 실패하였습니다.';
        }

        return response()->json([
            "success_fail_message" => $success_fail_message
        ]);

        //여기에서 홈으로 리다이렉트 하기(axios 쪽에서 함)

    }

    public function brand_edit()
    {
        $brands = Brand::all();
        $seller = auth()->user();
        // config\auth.php 의 설정이 auth 부르면 App\Seller 모델 부른다는 뜻임

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
