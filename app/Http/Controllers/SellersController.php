<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class SellersController extends Controller
{
    public function create()
    {
        $brands = DB::table('mall_brands')->get();
        //이렇게하면 모델 필요 없는데?? DB파사드 말고 모델 사용하는거 생각해보자

        return view('sellers.create', ['brands' => $brands]);
        // with() compact() 의 방법도 보고 왜 이거썼는지 생각해보자
    }

    public function store(Request $request)
    {
        $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:mall_sellers,email', //unique 검사란??
                'password' => 'required|confirmed|min:6' //패스워드 confirm 하는 필드가 입력값중 있어야한다는 뜻
                //https://laravel.kr/docs/6.x/validation 참고
        ]);

        $seller = \App\Mall_Seller::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'brand_id' => $request->input('brand_id')
        ]);
        // https://laravel.kr/docs/6.x/requests#%EC%9E%85%EB%A0%A5%EA%B0%92%20%EC%A1%B0%ED%9A%8C%ED%95%98%EA%B8%B0

        if($seller) {
            $request->session()->flash('회원가입 완료');
            return redirect('/');
        }
        else{
            $request->session()->flash('회원가입 실패');
        }
        //
    }
}
