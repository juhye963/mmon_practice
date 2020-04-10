<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        if(!auth()->attempt($request->only('email','password')))
        {
            //flash('로그인 정보 불일치');
            return back()->withInput();
        }

        //flash(auth()->user()->name. '님 환영합니다.');

        return redirect()->intended('home');

        //return $request->all(); 사용자의 입력정보 넘어오는지 체크
    }

    public function destroy()
    {
        auth()->logout();
        //flash('로그아웃되었습니다.'); App\Http\Controllers\flash() 라는 함수가 없다는 오류 뜸
        //Alert::success('성공','로그아웃 완료'); 컨트롤러에 Alert 클래스도 없음
        //$request -> session() ->flash 이렇게 쓰는거여따..
        //이 코드 아닌 방식으로 로그아웃하는 방법 찾아보기

        return redirect('/');
    }
}
