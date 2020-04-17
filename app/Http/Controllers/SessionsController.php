<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['except'=>'destroy']);
    }

    public function create()
    {
        return view('sessions.create');
        //return view('welcome');
    }

    public function store(Request $request)
    {
        //Illuminate\Http\Request 객체의 validate 메서드
        //사용자가 입력한 정보가 아래의 룰에 맞는지 체크
        $this->validate($request,[
            'email'=>'required|email',
            'password'=>'required|min:6',
        ]);

        if(!auth()->attempt($request->only('email','password'))){
            //flash('로그인 정보 불일치');
            //flash('로그인정보 불일치')->warning();
            return back()->withInput();
        }

        return redirect('/');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        //로그아웃 후 뒤로가기 누르면 이전 로그인 남아있는 상태 해결 위한 코드
        //forget() 은 세션에서 데이터 삭제. 세션의 모든 데이터 삭제는 flush()
        //공식문서의 세션 챕터 참조
        //새로 로그인하면 이전것은 사라지지만.. 마지막 유저의 세션정보 그대로 남아있는듯...
        $request->session()->flush();

        return redirect('/');
    }
}
