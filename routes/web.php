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

Route::get('/products/all-update-log/{product_id}', [
    'as' => 'product.all.update.log',
    'uses' => 'ProductsController@showAllUpdateLogs'
]);

/* 상품 삭제 /{product_id}*/
Route::delete('/products/destroy/{product_id}', [
    'as' => 'products.destroy',
    'uses' => 'ProductsController@destroy'
])->where('product_id', '[0-9]+');
//잘못된 접근 : "Symfony\Component\HttpKernel\Exception\NotFoundHttpException" 발생

Route::delete('/products/destroy-many', [
    'as' => 'products.destroy-many',
    'uses' => 'ProductsController@destroyMany'
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

Route::get('/show-categories-select',[
    'as' => 'categories.select',
    'uses' => 'ProductsController@selectCategoryToUpdateSelectedProduct'
]);

Route::post('/update-selected-product', [
    'as' => 'update.category.selected.products',
    'uses' => 'ProductsController@changeCategoryOfSelectedProducts'
]);

Route::post('/update-searched-product', [
    'as' => 'update.category.searched.products',
    'uses' => 'ProductsController@changeCategoryOfSearchedProducts'
]);


/*카테고리*/

Route::get('/categories/display-sub-categories', [
    'as' => 'categories.display-sub-categories',
    'uses' => 'CategoriesController@displaySubCategories'
]);

//카테고리 할인
Route::get('/category-discount-create', [
    'as' => 'category.discount.create',
    'uses' => 'CategoriesController@createCategoryDiscount'
]);

Route::post('/category-discount-store', [
    'as' => 'category.discount.store',
    'uses' => 'CategoriesController@storeCategoryDiscount'
]);

Route::get('/category-discount-list', [
    'as' => 'category.discount.list',
    'uses' => 'CategoriesController@listCategoryDiscount'
]);

Route::get('/category-discount-edit/{category_discount_id}', [
    'as' => 'category.discount.edit',
    'uses' => 'categoriesController@editCategoryDiscount'
]);

Route::post('/category-discount-update', [
    'as' => 'category.discount.update',
    'uses' => 'categoriesController@updateCategoryDiscount'
]);

Route::get('/category-discount-target-product', [
    'as' => 'category.discount.target.product',
    'uses' => 'CategoriesController@showTargetProductOfCategoryDiscount'
]);


/*브랜드 할인*/

Route::get('/brand-discount-list', [
    'as' => 'brand.discount.list',
    'uses' => 'DiscountController@listBrandDiscounts'
]);

Route::get('/brand-discount-create', [
    'as' => 'brand.discount.create',
    'uses' => 'DiscountController@createBrandDiscount'
]);

Route::post('/brand-discount-store', [
    'as' => 'brand.discount.store',
    'uses' => 'DiscountController@storeBrandDiscount'
]);

Route::get('/brand-discount-target-product', [
   'as' => 'brand.discount.target.product',
    'uses' => 'DiscountController@showTargetProductOfBrandDiscount'
]);

Route::get('/brand-discount-edit/{brand_discount_id}', [
    'as' => 'brand.discount.edit',
    'uses' => 'DiscountController@editBrandDiscount'
]);

Route::post('/brand-discount-update', [
   'as' => 'brand.discount.update',
   'uses' => 'DiscountController@updateBrandDiscount'
]);

//할인 제외상품
Route::get('/brand-discount-exclusion-products-create', [
    'as' => 'brand.discount.exclusions.create',
    'uses' => 'DiscountController@createBrandDiscountExcludedProducts'
]);

Route::post('/brand-discount-exclusion-products-store', [
    'as' => 'brand.discount.exclusions.store',
    'uses' => 'DiscountController@storeBrandDiscountExcludedProducts'
]);

Route::post('/category-discount-exclusion-products-store', [
    'as' => 'category.discount.exclusions.store',
    'uses' => 'DiscountController@storeCategoryDiscountExcludedProducts'
]);

Route::get('/brand-discount-exclusion-target-products', [
    'as' => 'brand.discount.exclusion.targets',
    'uses' => 'DiscountController@displaySearchedProductsForDiscountExclusions'
]);

/*상품통계*/
Route::get('/products-statistics', [
   'as' => 'products.statistics',
   'uses' => 'ProductsController@showProductsStatistics'
]);






/* 대량 데이터 */
Route::get('/many-brands', 'BrandsController@insertManyBrands');
Route::get('/many-sellers', 'SellersController@insertManySellers');
Route::get('/many-categories', 'CategoriesController@insertManyCategories');
Route::get('/many-products', 'ProductsController@insertManyProducts')->name('many.products');
Route::get('/empty-all', function () {

    if (config('database.default') !== 'sqlite') {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
    }

    //App\Brand::truncate();

    //App\Seller::truncate();

    //App\Category::truncate();

    //App\Product::truncate();

    //App\UpdateLog::truncate();

    //App\BrandProductDiscount::truncate();
    App\CategoryProductDiscount::truncate();


    if (config('database.default') !== 'sqlite') {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
});


Route::get('/test', function () {

    $sellers = \App\Seller::find(1500)->with(['productsByStatus'])->get();
    //$value = \App\Seller::find(1500)->products->groupBy('status');
    dd($sellers);

    /*//$test = \App\Seller::find(1500)->products->groupBy('status')->get();

    //0 = selling, 1 = stop_selling, 2 = sold_out
    $value = \App\Seller::with(['products', 'productsByStatus'])->find(1500)->productsByStatus;
    dd(json_encode($value));*/

    /*$brand = \App\Brand::find(1)->productCategories;
    dd($brand);*/
    /*$total = \App\Seller::find(1500)->products->count();
    $sold_out = \App\Seller::find(1500)->productsByStatus('stop_selling')->count();
    dd($sold_out);*/

    /*$product = \App\Product::find(69840)->categoryDiscountExclusion;

    dd($product);*/

/*
    $category_discount = \App\Product::find(71200)->categoryProductDiscount->discount_percentage;
    $brand_discount = \App\Product::find(71200)->brandProductDiscount->discount_percentage;
    dd($brand_discount.'+'.$category_discount);*/


    //dd(\App\Product::find(180)->getMostRecentBrandDiscount()->discount_percentage);

   /* $targetProductsOfBrandDiscount = App\Brand::find(2)->products
        ->where('price', '>=', 200)->all();

    $discount_percentage = 20/100;

    foreach ($targetProductsOfBrandDiscount as $targetProduct) {
        $targetProduct->discounted_price = $targetProduct->price * $discount_percentage;
    }*/

   /* $test = \App\BrandProductDiscount::find(3)->getTotalCountOfDiscountTargetProducts();
    dd($test);*/
/*
    $product = App\Product::find(18964);
    $description = $product->updateLogs()->first()->log_description;
    dd(str_replace('\n', '<br>', $description));
    //dd(nl2br($product->updateLogs()->first()->log_description));
    //return nl2br(e($product->updateLogs()->first()->log_description));*/

/*
    $logs = App\Product::find(18964)->updateLogs()->with('seller')->orderBy('updated_at', 'desc')->limit(3)->get();
    $data_set = [];

    //dd($logs);
    $i = 0;
    foreach ($logs as $log) {
        $data_set['seller'][$i] = $log->seller->name;
        $data_set['description'][$i] = $log->log_description;
        $i++;
    }

    dd($data_set);*/

    /*app('debugbar')->disable();
    print 'abc';*/
    /*$faker = \Faker\Factory::create();
    $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
    dd($faker->productName);*/

    //dd(App\Category::all()->random(1)->first()->id);

    //dd(App\Seller::where('brand_id', '!=', null));

    /*$status_enum_value = array('selling', 'stop_selling', 'sold_out');
    $rand_key = array_rand($status_enum_value, 1);
    dd($status_enum_value[$rand_key]);*/

    //dd(App\Seller::where('brand_id', '!=', null)->get()->random(1)->first()->id);

    /*$faker = \Faker\Factory::create();
    $price = $faker->numberBetween($min = 0, $max = 1000000);
    $discount_in_percentage  = $faker->numberBetween($min = 0, $max = 99);
    $discounted_price = $discount_in_percentage ? $price*($discount_in_percentage/100) : $price;

    dd($discounted_price);*/

    //dd(config('database.default') !== 'sqlite');

    /*$status_enum_value = array('selling', 'stop_selling', 'sold_out');
    //$rand_key = array_rand($status_enum_value, 1);
    dd(\Illuminate\Support\Arr::random($status_enum_value));*/

    /*$faker = \Faker\Factory::create();
    $random_image = $faker->image(storage_path('app/public/product_image'), 400, 200, null, false);
    dd($random_image);*/
});









