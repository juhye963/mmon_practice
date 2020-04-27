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

Route::get('/', [
    'as' => 'home',
    'uses' => 'HomeController@index'
]);


/*회원가입*/
Route::get('/register', [
    'as' => 'sellers.create',
    'uses' => 'SellersController@create'
]);

/*이메일이 아이디처럼 쓰이고 있음.
아이디중복체크 대신 이메일 중복체크*/
Route::get('/sellers/check-if-email-has-been-taken', [
    'as' => 'sellers.check-if-email-has-been-taken',
    'uses' => 'SellersController@emailDuplicateCheck'
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
])->middleware('auth');

Route::post('/products/store', [
    'as' => 'products.store',
    'uses' => 'ProductsController@store'
])->middleware('auth');

/*상품 display*/
Route::get('/products/index', [
    'as' => 'products.index',
    'uses' => 'ProductsController@index'
]);

Route::post('/products/image-upload', [
    'as' => 'products.image-upload',
    'uses' => 'ProductsController@imageUpload'
]);

/* 상품 삭제 /{product_id}*/
Route::delete('/products/destroy/{product_id}', [
    'as' => 'products.destroy',
    'uses' => 'ProductsController@destroy'
]);


/* 상품 수정 */
Route::get('/products/edit/{product_id}', [
    'as' => 'products.edit',
    'uses' => 'ProductsController@edit'
]);

Route::post('/products/update', [
    'as' => 'products.update',
    'uses' => 'ProductsController@update'
]);


/*카테고리*/
Route::get('/categories/select',[
    'as' => 'categories',
    'uses' => 'CategoriesController@select'
]);

Route::get('/categories/display-sub-categories',[
    'as' => 'categories.display-sub-categories',
    'uses' => 'CategoriesController@displaySubCategories'
]);









