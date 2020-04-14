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

/*메인화면*/

Route::get('/',[
    'as' => 'home',
    'uses' => 'HomeController@index'
]);


/*회원가입*/
Route::get('/register', [
    'as' => 'sellers.create',
    'uses' => 'SellersController@create'
]);

Route::post('/register', [
    'as' => 'sellers.store',
    'uses' => 'SellersController@store'
]);


/*로그인 로그아웃*/
Route::get('/login', [
    'as' => 'sessions.create',
    'uses' => 'SessionsController@create'
]);
Route::get('/logout', [
    'as' => 'sessions.destroy',
    'uses' => 'SessionsController@destroy'
]);
Route::post('/login', [
    'as' => 'sessions.store',
    'uses' => 'SessionsController@store'
]);


//브랜드명 확인하는 곳
/*Route::get('/brands',[
    'as' => 'brands',
    'uses' => 'BrandsController@index'
]);*/

/*셀러의 브랜드 수정*/
Route::get('/seller/brands/edit', [
    'as' => 'seller.brand.edit',
    'uses' => 'SellersController@brand_edit'
]);
Route::post('/seller/brands/update', [
   'as' => 'seller.brand.update',
   'uses' => 'SellersController@brand_update'
]);


/*상품등록*/
Route::get('/products/create', [
    'as' => 'products.create',
    'uses' => 'ProductsController@create'
]);
Route::post('/products/store', [
    'as' => 'products.store',
    'uses' => 'ProductsController@store'
]);
//카테고리 정보 test 페이지
/*Route::get('/categories',[
    'as' => 'categories',
    'uses' => 'CategoriesController@index'
]);*/

/*상품 display*/
Route::get('/products/show',[
    'as' => 'products.show',
    'uses' => 'ProductsController@show'
]);







