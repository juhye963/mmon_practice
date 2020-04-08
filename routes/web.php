<?php
use Illuminate\Support\Facades\Cache;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('test');
});

Route::get('/home',function(){
    return view('home');
});

/*로그인 로그아웃*/
Route::get('/login','SessionsController@create');
Route::get('/logout','SessionsController@destroy');

Route::post('/login','SessionsController@store');

