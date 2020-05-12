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
        $this->validate($request,[
            'email'=>'required|email',
            'password'=>'required|min:6',
        ]);

        if(!auth()->attempt($request->only('email','password'))){
            return back()->withInput();
        }

        return redirect('/');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->flush();

        return redirect('/');
    }
}
