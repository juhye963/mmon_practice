<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Product;
use App\Seller;
use App\UpdateLog;
use http\Env\Response;
use http\Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Arr;


class ProductsController extends Controller
{
    public function create()
    {
        $categories = Category::where('pid', '=', '0')->get();
        $product_status = [
            'selling' => '판매중',
            'stop_selling' => '판매중지',
            'sold_out' => '일시품절'
        ];

        return view('products.create')->with([
            'categories' => $categories,
            'product_status' => $product_status
        ]);
    }

    public function store(Request $request)
    {

        //dd(gettype($request->input('product_price')));
        $validatedData = $request->validate([
            'product_name' => 'required|max:255',
            'product_price' => 'required|min:0|max:1000000',
            'product_discounted_price' => 'required|min:0|max:1000000|lte:product_price',
            'product_stock' => 'required|min:0|max:1000',
            'category_pid' => 'required:sub_category_id|exists:mall_categories,id',
            'sub_category_id' => 'required_with:category_pid|exists:mall_categories,id',
            'product_status' => [
                'required',
                Rule::in(['selling', 'stop_selling', 'sold_out']),
            ],
            //'product_image' => 'file|image'
        ]);

        dd('hi');

        $product = new Product();

        try {
            $product->name = $request->product_name;
            $product->price = $request->product_price;
            $product->discounted_price = $request->product_discounted_price;
            $product->stock = $request->product_stock;
            $product->seller_id = auth()->user()->id;
            $product->brand_id = auth()->user()->brand_id;
            $product->category_id = $request->sub_category_id;
            $product->status = $request->product_status;

            $product->save();

            if ($request->hasFile('product_image')) {
                $path = $request->file('product_image')->storeAs('public/product_image', $product->id.'.png');
            }

            $success_fail_status = 'success' ; //이것들 나중에 상수로 정의?

        } catch (QueryException $queryException) {
            $success_fail_status = 'query_fail';
        }

        return response()->json([
            'success_fail_status' => $success_fail_status
        ]);

    }

    public function showProductsStatistics() {

        //셀러를 기준으로 상품상태별 제품수
        $sellers = DB::table('mall_sellers')
            ->leftJoin('mall_products', 'mall_sellers.id', '=', 'mall_products.seller_id')
            ->select('mall_sellers.id', 'mall_sellers.name', 'mall_products.status', DB::raw("COUNT(*) AS count"))
            ->groupBy('mall_sellers.id', 'mall_products.status')
            ->get();

        //카테고리를 기준으로 상품상태별 제품수
        $categories = DB::table('mall_categories')
            ->leftJoin('mall_products', 'mall_categories.id', '=', 'mall_products.category_id')
            ->select('mall_categories.id',
                'mall_categories.name',
                'mall_products.status',
                DB::raw("COUNT(*) AS count"),
                )->groupBy('mall_categories.id', 'mall_products.status')
            ->get();

        //카테고리기준 브랜드별, 상품상태별 제품수
        $categoryProductByBrands = DB::table('mall_categories')
            ->leftJoin('mall_products', 'mall_categories.id', '=', 'mall_products.category_id')
            ->leftJoin('mall_brands', 'mall_brands.id', '=', 'mall_products.brand_id')
            ->select('mall_categories.id',
                DB::raw("mall_categories.name AS category_name"),
                DB::raw("mall_brands.id AS brand_id"),
                DB::raw("mall_brands.name AS brand_name"),
                'mall_products.status',
                DB::raw("COUNT(*) AS count"))
            ->groupBy('mall_categories.id', 'mall_brands.id', 'mall_products.status')
            ->get();

        //브랜드 기준으로 상품상태별 제품수
        $brands = DB::table('mall_brands')
            ->leftJoin('mall_products', 'mall_brands.id', '=', 'mall_products.brand_id')
            ->select('mall_brands.id',
                'mall_brands.name',
                'mall_products.status',
                DB::raw("COUNT(*) AS count"),
                )->groupBy('mall_brands.id', 'mall_products.status')
            ->get();

        //브랜드 기준으로 카테고리별 상품상태별 제품수
        $brandProductByCategories = DB::table('mall_brands')
            ->leftJoin('mall_products', 'mall_brands.id', '=', 'mall_products.brand_id')
            ->leftJoin('mall_categories', 'mall_categories.id', '=', 'mall_products.category_id')
            ->select('mall_brands.id',
                DB::raw("mall_brands.name AS brand_name"),
                DB::raw("mall_categories.id AS category_id"),
                DB::raw("mall_categories.name AS category_name"),
                'mall_products.status',
                DB::raw("COUNT(*) AS count"))
            ->groupBy('mall_brands.id', 'mall_categories.id', 'mall_products.status')
            ->get();

        //브랜드별 데이터
        $brand_statistics = [];
        /*브랜드기준 카테고리별, 상품상태별 제품수 데이터 형태
        $category_statistics['id'] = [
            'name' => '이름',
            'total' => '숫자',
            'selling' => '숫자',
            'stop_selling' => '숫자',
            'sold_out' => '숫자',
            'brand_id' => [
                'brand_name' => '이름',
                'total' => '숫자',
                'selling' => '숫자',
                'stop_selling' => '숫자',
                'sold_out' => '숫자'
            ]
        ];
        */
        foreach ($brands as $brand) {
            if (empty($brand_statistics[$brand->id]) == true) {
                $brand_statistics[$brand->id] = ['brand_id' => $brand->id, 'brand_name' => $brand->name];
                $brand_statistics[$brand->id] += ['total' => 0];
            }
            $brand_statistics[$brand->id][$brand->status] = $brand->count;
            $brand_statistics[$brand->id]['total'] = (int)($brand_statistics[$brand->id]['total']) + (int)($brand->count);

            //집계결과 없다면 0
            if(empty($brand_statistics[$brand->id]['selling'])) {
                $brand_statistics[$brand->id]['selling'] = 0;
            }
            if(empty($brand_statistics[$brand->id]['sold_out'])) {
                $brand_statistics[$brand->id]['sold_out'] = 0;
            }
            if(empty($brand_statistics[$brand->id]['stop_selling'])) {
                $brand_statistics[$brand->id]['stop_selling'] = 0;
            }
        }
        foreach ($brandProductByCategories as $brandCategory) {
            if (empty($brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]) == true) {
                $brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id] = ['category_id' => $brandCategory->category_id, 'category_name' => $brandCategory->category_name];
                $brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id] += ['total' => 0];
            }
            $brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id][$brandCategory->status] = $brandCategory->count;
            $brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['total'] =
                (int)($brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['total'])
                + (int)($brandCategory->count);

            //집계 결과가 없는 경우 0
            if(empty($brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['selling'])) {
                $brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['selling'] = 0;
            }
            if(empty($brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['sold_out'])) {
                $brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['sold_out'] = 0;
            }
            if(empty($brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['stop_selling'])) {
                $brand_statistics[$brandCategory->id]['categories'][$brandCategory->category_id]['stop_selling'] = 0;
            }
        }

        //카테고리별 데이터
        $category_statistics = [];
        /*카테고리기준 브랜드별, 상품상태별 제품수 데이터 형태
        $category_statistics['id'] = [
            'name' => '이름',
            'total' => '숫자',
            'selling' => '숫자',
            'stop_selling' => '숫자',
            'sold_out' => '숫자',
            'brand_id' => [
                'brand_name' => '이름',
                'total' => '숫자',
                'selling' => '숫자',
                'stop_selling' => '숫자',
                'sold_out' => '숫자'
            ]
        ];
        */
        foreach ($categories as $category) {
            if (empty($category_statistics[$category->id]) == true) {
                $category_statistics[$category->id] = ['category_id' => $category->id, 'category_name' => $category->name];
                $category_statistics[$category->id] += ['total' => 0];
            }
            $category_statistics[$category->id][$category->status] = $category->count;
            $category_statistics[$category->id]['total'] = (int)($category_statistics[$category->id]['total']) + (int)($category->count);

            //집계결과 없다면 0
            if(empty($category_statistics[$category->id]['selling'])) {
                $category_statistics[$category->id]['selling'] = 0;
            }
            if(empty($category_statistics[$category->id]['sold_out'])) {
                $category_statistics[$category->id]['sold_out'] = 0;
            }
            if(empty($category_statistics[$category->id]['stop_selling'])) {
                $category_statistics[$category->id]['stop_selling'] = 0;
            }
        }

        foreach ($categoryProductByBrands as $categoryBrand) {
            if (empty($category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]) == true) {
                $category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id] = ['brand_id' => $categoryBrand->brand_id, 'brand_name' => $categoryBrand->brand_name];
                $category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id] += ['total' => 0];
            }
            $category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id][$categoryBrand->status] = $categoryBrand->count;
            $category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['total'] =
                (int)($category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['total'])
                + (int)($categoryBrand->count);

            //집계 결과가 없는 경우 0
            if(empty($category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['selling'])) {
                $category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['selling'] = 0;
            }
            if(empty($category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['sold_out'])) {
                $category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['sold_out'] = 0;
            }
            if(empty($category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['stop_selling'])) {
                $category_statistics[$categoryBrand->id]['brands'][$categoryBrand->brand_id]['stop_selling'] = 0;
            }
        }


        //셀러별 데이터
        $seller_statistics = [];
        /*셀러 기준 상품상태별 제품수 데이터 형태
         * $seller_statistics = [
            'seller_id' => [
                'name' => '이름',
                'selling' => '숫자',
                'stop_selling' => '숫자',
                'sold_out' => '숫자'
            ],
        ];
        */
        foreach ($sellers as $seller) {
            if (empty($seller_statistics[$seller->id]) == true) {
                $seller_statistics[$seller->id] = ['seller_id' => $seller->id, 'seller_name' => $seller->name];
                $seller_statistics[$seller->id] += ['total' => 0];
            }
            $seller_statistics[$seller->id][$seller->status] = $seller->count;
            $seller_statistics[$seller->id]['total'] = (int)($seller_statistics[$seller->id]['total']) + (int)($seller->count);
            if(empty($seller_statistics[$seller->id]['selling'])) {
                $seller_statistics[$seller->id]['selling'] = 0;
            }
            if(empty($seller_statistics[$seller->id]['stop_selling'])) {
                $seller_statistics[$seller->id]['stop_selling'] = 0;
            }
            if(empty($seller_statistics[$seller->id]['sold_out'])) {
                $seller_statistics[$seller->id]['sold_out'] = 0;
            }
        }

        return view('products.statistics')->with([
            'sellers' => $seller_statistics,
            'categories' => $category_statistics,
            'brands' => $brand_statistics,
        ]);
    }

    public function index(Request $request)
    {
        /* view 에 필요한 변수 설정 */
        $search_types = [
            'prds_nm' => '상품명',
            'seller_nm' => '판매자 이름',
            'brand_nm' => '브랜드명'
        ];

        $sorts = [
            'recent' => '최근 등록 순',
            'price_asc' => '낮은 가격 순',
            'price_desc' => '높은 가격 순',
            'prds_nm_asc' => '상품명 순'
        ];

        $prds_status = [
            'selling' => '판매중',
            'stop_selling' => '판매중지',
            'sold_out' => '일시품절'
        ];


        $products = Product::with(
            'brand',
            'category',
            'seller',
            'updateLogs',
            'brandProductDiscount',
            'categoryProductDiscount',
            'brandDiscountExclusion',
            'categoryDiscountExclusion',
        );

        $parameters = $request->only('search_type', 'search_word', 'sort', 'prds_status', 'start_date', 'end_date');

        $parameters['search_type'] = $request->input('search_type', '');
        $parameters['search_word'] = $request->input('search_word', '');
        $parameters['sort'] = $request->input('sort', '');
        $parameters['prds_status'] = $request->input('prds_status', []); // 상품상태는 배열로 들어옴
        $parameters['start_date'] = $request->input('start_date', '');
        $parameters['end_date'] = $request->input('end_date', '');

        /*validation*/
        //검색어 있을때는 검색유형 필수
        $request->validate([
           'search_type' => [
               'required_with:search_word',
                Rule::in(['prds_nm', 'seller_nm', 'brand_nm', '']),
           ],
        ]);

        //현재보다 미래의 날짜는 입력할 수 없음 (날짜 찍히는거 보고 잘 비교하기)
        //날짜 한쪽이 입력되면 다른 한쪽도 필요함
        if ($parameters['start_date'] != '' || $parameters['end_date'] != '') {
            $request->validate([
                'start_date' => 'required|date|before_or_equal:end_date',
                'end_date' => 'required|date|before_or_equal:today',

            ]);
        }


        /*조건적용*/
        //검색키워드로 찾기
        //상품명으로 검색 아닐시에는 검색유형(relation)에서 name으로 검색하게됨
        if ($parameters['search_type'] == 'prds_nm') {
            $products = $products->where('name', 'LIKE', '%' . $parameters['search_word'] . '%');
        } elseif ($parameters['search_type'] == 'seller_nm') {
            $products = $products->whereHas('seller', function (Builder $query) use ($parameters) {
                $query->where('name', 'LIKE', '%' . $parameters['search_word'] . '%');
            });
        } elseif ($parameters['search_type'] == 'brand_nm') {
            $products = $products->whereHas('brand', function (Builder $query) use ($parameters) {
                $query->where('name', 'LIKE', '%' . $parameters['search_word'] . '%');
            });
        }else {
            $products = $products;
        }

        if ($parameters['prds_status'] != []) {
            $products = $products->whereIn('status', $parameters['prds_status']);
        }

        //날짜검색 있을때
        // 위에서 하나라도 공백이 아닐시에는 required 로 조건 맞춰줌. = 둘 다 공백이거나 둘 다 값이 있는 상태가 됨
        if ($parameters['start_date'] != '' && $parameters['end_date'] != '' ) {
            $start_date = date('Y-m-d H:i:s', strtotime($parameters['start_date']));
            $end_date = date('Y-m-d H:i:s', strtotime("+1 days -1 second", strtotime($parameters['end_date'])));
            $products = $products->whereBetween('created_at', [$start_date, $end_date]);
        }


        //정렬조건 붙이기
        switch ($parameters['sort']) {
            case 'recent':
                $products = $products->orderByDesc('updated_at');
                break;
            case 'price_asc' :
                $products = $products->orderBy('price');
                break;
            case 'price_desc' :
                $products = $products->orderByDesc('price');
                break;
            case 'prds_name':
                $products = $products->orderBy('name');
                break;
            default :
                $products = $products->orderByDesc('id');
                break;
        }

        $products = $products->paginate(20);

        return view('products.index')->with([
            'products' => $products,
            'parameters' => $parameters,
            'search_types' => $search_types,
            'sorts' => $sorts,
            'prds_status' => $prds_status,
        ]);
    }

    public function showAllUpdateLogs($product_id) {
        $productUpdateLogsAll = Product::with('seller')->find($product_id)->updateLogs;
        return view('products.show-all-update-logs-of-product', ['product_update_logs_all' => $productUpdateLogsAll]);
    }

    public function edit($product_id) {

        $current_seller_id = auth()->user()->id;
        $product = Product::find($product_id);
        if ($product == null) {
            return redirect()->route('products.index')->withErrors(['잘못된 접근입니다.']);
        }
        $product_seller_id = $product->seller->id;

        if ($current_seller_id != $product_seller_id) {
            return Redirect::back()->withErrors(['수정권한이 없습니다.']);
        }

        $categories = Category::where('pid', '=', '0')->get();
        $product_status = [
            'selling' => '판매중',
            'stop_selling' => '판매중지',
            'sold_out' => '일시품절'
        ];

        return view('products.edit')->with([
            'categories' => $categories,
            'product_status' => $product_status,
            'product' => $product
        ]);
    }

    public function update(Request $request) {

        date_default_timezone_set('Asia/Seoul');

        $validatedData = $request->validate([
            'product_name' => 'required|max:255',
            'product_price' => 'required|numeric|min:1|max:1000000',
            'product_discounted_price' => 'required|numeric|min:1|max:1000000|lte:product_price',
            'product_stock' => 'required|numeric|min:0|max:100000',
            'category_pid' => 'required:sub_category_id|exists:mall_categories,id',
            'sub_category_id' => 'required_with:category_pid|exists:mall_categories,id',
            'product_status' => [
                'required',
                Rule::in(['selling', 'stop_selling', 'sold_out']),
            ],
        ]);

        if ($request->product_image instanceof UploadedFile) {
            $request->validate([
                'product_image' => 'file|image'
            ]);
        }

        $updated_product_data = $request->only(
            'product_name',
            'product_price',
            'product_discounted_price',
            'product_stock',
            'sub_category_id',
            'product_status'
        );

        $update_log_description = '';

        $product_to_be_updated = Product::with(['category'])->find($request->input('product_id'));

        $product_variable_name_set = [
            'product_name' => ['column_name' => 'name', 'log_name' => '상품명'],
            'product_price' => ['column_name' => 'price', 'log_name' => '상품가격'],
            'product_discounted_price' => ['column_name' => 'discounted_price', 'log_name' => '할인가'],
            'product_stock' => ['column_name' => 'stock', 'log_name' => '재고'],
            'sub_category_id' => ['column_name' => 'category_id', 'log_name' => '카테고리'],
            'product_status' => ['column_name' => 'status', 'log_name' => '판매상태'],
        ];


        foreach ($product_variable_name_set as $input_name => $value) {
            $updated_data = $updated_product_data[$input_name];
            $column_name = $value['column_name'];
            $original_data = $product_to_be_updated->$column_name;

            if ($input_name == 'sub_category_id' && $updated_data != $original_data) {

                $original_category_name = $product_to_be_updated->category->name;
                $updated_category_name = Category::find($updated_product_data['sub_category_id'])->name;
                $update_log_description .= $value['log_name'] . " : " . $original_category_name . " -> " . $updated_category_name . chr(10);
                $product_to_be_updated->$column_name = $updated_data;

            } else if ($updated_data != $original_data) {
                $update_log_description .= $value['log_name'] . " : " . $original_data . " -> " . $updated_data . chr(10);
                $product_to_be_updated->$column_name = $updated_data;
            }

        }

        $current_seller_id = auth()->user()->id;
        $now = date("Y-m-d H:i:s");

        $update_log = new UpdateLog();

        try {
            $product_to_be_updated->save();

            $update_log::insert([
                'seller_id' => $current_seller_id,
                'product_id' => $product_to_be_updated->id,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'log_description' => $update_log_description,
                'updated_at' => $now
            ]);

            if ($request->product_image instanceof UploadedFile) {
                $request->file('product_image')->storeAs('public/product_image', $request->product_id.'.png');
                Cache::flush();
                // update 한 새로운 이미지 가져오기 위해 캐시 삭제
                //php artisan config:cache 필요
            }

            $success_fail_status = 'success' ;

        } catch (QueryException $queryException) {
            $success_fail_status = 'query_fail';
        }

        return response()->json([
            'success_fail_status' => $success_fail_status
        ]);


    }

    public function destroy(Request $request, string $product_id)
    {
        $product_to_delete = Product::findOrFail($product_id);
        //예외를 처리하지 않는다면 404 HTTP 응답이 자동으로 사용자에게 보내집니다.

        throw_if( $product_to_delete->seller_id != auth()->user()->id,
            AuthorizationException::class,
            '상품을 삭제할 권한이 없습니다.'
        );//code 403

        throw_if(
            method_exists($product_to_delete, 'trashed') == false,
            \Exception::class,
            '소프트 삭제 할 수 없어 삭제가 취소되었습니다. 관리자에게 문의 바랍니다.'
        );

        $product_to_delete->delete();
        return response()->json([]);
    }

    public function destroyMany(Request $request) {

        //dd(count($request->input('product_ids_for_deletion')));
        $validator = Validator::make($request->only('product_ids_for_deletion'), [
            'product_ids_for_deletion' => 'required|array|min:1',
            'product_ids_for_deletion.*' => 'required|exists:mall_products,id'
        ]);

        $product_ids_to_delete = $request->input('product_ids_for_deletion');
        $current_login_seller = auth()->user()->id;
        $is_my_product = Product::whereIn('id', $product_ids_to_delete)->where('seller_id', '!=', $current_login_seller)->first();
        //dd($is_my_product); first()는 첫번째모델 or null return

        throw_if(
            $is_my_product != null,
            AuthorizationException::class,
            '삭제 권한이 없는 상품이 포함되어있습니다.'
        ); //403

        throw_if(
            method_exists(Product::whereIn('id', $product_ids_to_delete)->first(), 'trashed') == false,
            \Exception::class,
            '소프트 삭제 할 수 없어 삭제가 취소되었습니다. 관리자에게 문의 바랍니다.'
        ); //500

        Product::whereIn('id', $product_ids_to_delete)->delete();

        return response()->json([]);

    }

    public function insertManyProducts() {

        app('debugbar')->disable();
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit','512M');

        $product = new Product();
        $productDataSet = [];
        $categories = Category::all();
        //$category = Category::first();
        $sellers = Seller::where('brand_id', '!=', null)->get();
        $status_enum_value = array('selling', 'stop_selling', 'sold_out');


        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < 1000; $j++) {

                $random_seller = $sellers->random(1)->first();

                $random_price = rand(0, 1000000);
                $random_discount_percentage = rand(0,100);
                $discounted_price = $random_price;
                if ($random_discount_percentage != 0) {
                    $discounted_price = $random_price * ($random_discount_percentage / 100);
                }

                $date_range_start = strtotime('-10 days');
                $date = date('Y-m-d H:i:s', mt_rand($date_range_start, time()));

                $productDataSet[$j] =  [
                    'name' => $product->makeRandomKoreanProductName(),
                    'price' => $random_price,
                    'discounted_price' => $discounted_price,
                    'seller_id' => $random_seller->id,
                    'stock' => rand(0, 16777215),
                    'status' => Arr::random($status_enum_value),
                    'category_id' => $categories->random(1)->first()->id,
                    //'category_id' => $category->id,
                    'brand_id' => $random_seller->brand_id,
                    //'brand_id' => Brand::first()->id,
                    'created_at' => $date,
                    'updated_at' => $date
                ];
            }

            Product::insert($productDataSet);
            $productDataSet = []; //비우기
            dump("insert" . $i . " 번째. 메모리사용량(peak) : " . memory_get_peak_usage() . ", 메모리사용량(normal) : " . memory_get_usage() . ", 메모리사용량(true) : " . memory_get_peak_usage(true));
        }

        return "done";
    }

    public function selectCategoryToUpdateSelectedProduct()
    {
        $categories = Category::where('pid', '=', '0')->get();
        return view('products.show-category-select')->with([
            'categories' => $categories,
            'product_sub_category_id' => '',
            'product_parent_category_id' => '',
        ]);
    }

    public function changeCategoryOfSelectedProducts(Request $request) {
        $category = $request->input('selected_category_to_update_checked_products', '');
        $selectedProductId = $request->input('selected_products_to_change_category', []);

        $request->validate([
           'selected_category_to_update_checked_products' => 'required|exists:mall_categories,id',
        ]);

        foreach($request->get('selected_products_to_change_category') as $key => $value) {
            $request->validate([
               'selected_products_to_change_category.'.$key => 'required|exists:mall_products,id'
            ]);
        }
        //https://laravel.io/forum/11-12-2014-how-to-validate-array-input

        Product::whereIn('id', $selectedProductId)->update(['category_id' => $category]);

        return response()->json([]);

    }

    public function changeCategoryOfSearchedProducts(Request $request) {

        $products = Product::with('brand','category','seller');

        $parameters = $request->only('search_type', 'search_word', 'sort', 'prds_status', 'start_date', 'end_date', 'selected_category_to_update_searched_products');

        $parameters['search_type'] = $request->input('search_type', '');
        $parameters['search_word'] = $request->input('search_word', '');
        $parameters['sort'] = $request->input('sort', '');
        $parameters['prds_status'] = $request->input('prds_status', []); // 상품상태는 배열로 들어옴
        $parameters['start_date'] = $request->input('start_date', '');
        $parameters['end_date'] = $request->input('end_date', '');
        $parameters['selected_category_to_update_searched_products'] = $request->input('selected_category_to_update_searched_products', '');

        /*validation*/
        //검색어 있을때는 검색유형 필수
        $request->validate([
            'search_type' => [
                'required_with:search_word',
                Rule::in(['prds_nm', 'seller_nm', 'brand_nm', '']),
            ],
        ]);

        //현재보다 미래의 날짜는 입력할 수 없음 (날짜 찍히는거 보고 잘 비교하기)
        //날짜 한쪽이 입력되면 다른 한쪽도 필요함
        if ($parameters['start_date'] != '' || $parameters['end_date'] != '') {
            $request->validate([
                'start_date' => 'required|date|before_or_equal:end_date',
                'end_date' => 'required|date|before_or_equal:today',
            ]);
        }

        /*조건적용*/
        //검색키워드로 찾기
        //상품명으로 검색 아닐시에는 검색유형(relation)에서 name으로 검색하게됨
        if ($parameters['search_type'] == 'prds_nm') {
            //더 좋은 방법 있을듯(계속 생각해보기)
            $products = $products->where('name', 'LIKE', '%' . $parameters['search_word'] . '%');
        } elseif ($parameters['search_type'] == 'seller_nm') {
            $products = $products->whereHas('seller', function (Builder $query) use ($parameters) {
                $query->where('name', 'LIKE', '%' . $parameters['search_word'] . '%');
            });
        } elseif ($parameters['search_type'] == 'brand_nm') {
            $products = $products->whereHas('brand', function (Builder $query) use ($parameters) {
                $query->where('name', 'LIKE', '%' . $parameters['search_word'] . '%');
            });
        }else {
            $products = $products;
        }

        //체크박스!!
        if ($parameters['prds_status'] != []) {
            //dd(count($parameters['prds_status']));
            $products = $products->whereIn('status', $parameters['prds_status']);
        }

        //날짜검색 있을때
        // 위에서 하나라도 공백이 아닐시에는 required 로 조건 맞춰줌. = 둘 다 공백이거나 둘 다 값이 있는 상태가 됨
        if ($parameters['start_date'] != '' && $parameters['end_date'] != '' ) {
            $start_date = date('Y-m-d H:i:s', strtotime($parameters['start_date']));
            //dd($start_date);
            $end_date = date('Y-m-d H:i:s', strtotime("+1 days -1 second", strtotime($parameters['end_date'])));
            //dd ($parameters['end_date']);
            $products = $products->whereBetween('created_at', [$start_date, $end_date]);
        }

        //정렬조건 붙이기
        switch ($parameters['sort']) {
            case 'recent':
                $products = $products->orderByDesc('updated_at');
                break;
            case 'price_asc' :
                $products = $products->orderBy('price');
                break;
            case 'price_desc' :
                $products = $products->orderByDesc('price');
                break;
            case 'prds_name':
                $products = $products->orderBy('name');
                break;
            default :
                $products = $products->orderByDesc('id');
                break;
        }

        foreach ($products->get() as $product) {
            dump($product->id);
        }

        $products->update(['category_id' => $parameters['selected_category_to_update_searched_products']]);

        return response()->json([]);
    }


}
