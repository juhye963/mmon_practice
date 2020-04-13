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
})->name('home');

/*회원가입*/
// (라우트이름 지정하고 페이지에서 이름으로 찾도록 하는 방법 시도)
// 한 라우트에 컨트롤러 여러개 쓴답시고 'uses'에 배열 주면 에러뜬다(스트링 달라고함)
// 한 라우트에 두개 컨트롤러는 안된다함. http 리퀘스트 종류가 다르지 않은 이상.
Route::get('/register',[
    'as' => 'sellers.create', //이 이름으로 이 라우트 찾을거임
    'uses' => 'SellersController@create'
]);
//라우트에 컨트롤러 없으면 invalid route action 나온다..? why??
//get 라우트 쓸때는 method 이름을 특정해주어야한다는데..?(스택오버플로)
Route::post('/register',[
    'as' => 'sellers.store',
    'uses' => 'SellersController@store'
]);


/*로그인 로그아웃*/
Route::get('/login',[
    'as' => 'sessions.create',
    'uses' => 'SessionsController@create'
]);
Route::get('/logout',[
    'as' => 'sessions.destroy',
    'uses' => 'SessionsController@destroy'
]);

Route::post('/login',[
    'as' => 'sessions.store',
    'uses' => 'SessionsController@store'
]);
/*
Route::get('/brands',[
    'as' => 'brands',
    'uses' => 'BrandsController@index'
]);*/

/*브랜드 수정*/
Route::get('/brands/edit',[
    'as' => 'brands.edit',
    'uses' => 'BrandsController@edit'
]);

Route::post('/brands/update',[
   'as' => 'brands.update',
   'uses' => 'BrandsController@update'
]);






