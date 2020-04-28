<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Product;
use App\Seller;
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
use Illuminate\Validation\Rule;

class ProductsController extends Controller
{
    public function create()
    {
        //상품등록폼은 로그인상태에서만 올 수 있도록
        //공식문서 : 현재 사용자의 승인 여부 결정하기
        //https://laravel.kr/docs/6.x/authentication#protecting-routes

        //나중에 여기에서 또쓰지 말고 카테고리컨트롤러와 공유할 수 있도록 바꾸기.. how? 생각
        $categories = Category::where('pid', '=', '0')->get();
        $product_status = [
            'selling' => '판매중',
            'stop_selling' => '판매중지',
            'sold_out' => '일시품절'
        ];
        //상품상태는 나중에 @index에 있는 것과 공유하기

        return view('products.create')->with([
            'categories' => $categories,
            'product_status' => $product_status
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|max:255',
            'product_price' => 'required|digits_between:1,1000000',
            'product_discounted_price' => 'required|digits_between:1,1000000|lte:product_price',
            'product_stock' => 'required|min:0|max:1000',
            'category_pid' => 'required:sub_category_id|exists:mall_categories,id',
            'sub_category_id' => 'required_with:category_pid|exists:mall_categories,id',
            'product_status' => [
                'required',
                Rule::in(['selling', 'stop_selling', 'sold_out']),
            ],
            'product_image' => 'file|image'
        ]);

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
            //use Illuminate\Database\QueryException; 쓰지 않으면 여기에 걸리지 않음
        }
        //어느 예외가 더 큰건지 찾아보기(laravel api문서?)
        //두 예외가 모두 일어나는 상황에서 ErrorException이 먼저 걸림 = 이게 더 작은가?
        //auth middleware로 접근 막아서 비로그인 상태에서 나는 ErrorException 예외 안뜨도록 하고 캐치하지 않는걸로 바꿈

        return response()->json([
            'success_fail_status' => $success_fail_status
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


        //dd(asset(Storage::url('12.png')));
        $products = Product::with('brand','category','seller');

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

        //like %%빼기

        //날짜검색 있을때
        // 위에서 하나라도 공백이 아닐시에는 required 로 조건 맞춰줌. = 둘 다 공백이거나 둘 다 값이 있는 상태가 됨
        if ($parameters['start_date'] != '' && $parameters['end_date'] != '' ) {
            $start_date = date('Y-m-d H:i:s', strtotime($parameters['start_date']));
            //dd($start_date);
            $end_date = date('Y-m-d H:i:s', strtotime("+1 days -1 second", strtotime($parameters['end_date'])));
            //dd($end_date);
            //23:59:59 종료일 포함시키지 않는 문제 해결
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

        $products = $products->paginate(5);

        return view('products.index')->with([
            'products' => $products,
            'parameters' => $parameters,
            'search_types' => $search_types,
            'sorts' => $sorts,
            'prds_status' => $prds_status,
        ]);
    }

    public function edit($product_id) {

        //dd($product_id);
        $current_seller_id = auth()->user()->id;
        $product = Product::find($product_id);
        if ($product == null) {
            return redirect()->route('products.index')->withErrors(['잘못된 접근입니다.']);
        }
        $product_seller_id = $product->seller->id;

        if ($current_seller_id != $product_seller_id) {
            return Redirect::back()->withErrors(['수정권한이 없습니다.']);
            // 리다이렉트 후에 $errors 변수가 자동으로 뷰에서 공유되어 손쉽게 사용자에게 보여질 수 있습니다.
            // withErrors 메소드는 validator, MessageBag, 혹은 PHP array 를 전달 받습니다.
        }


        //dd($product->name);

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

        //dd($request->product_id);
        //dd($request->product_image instanceof UploadedFile); //undefined or  Illuminate\Http\UploadedFile
        $validatedData = $request->validate([
            'product_name' => 'required|max:255',
            'product_price' => 'required|digits_between:1,1000000',
            'product_discounted_price' => 'required|digits_between:1,1000000|lte:product_price',
            'product_stock' => 'required|min:0|max:1000',
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

        $product_to_be_updated = Product::find($request->product_id);
        try {
            //좀 더 확실하게 input('이름')으로 -> 아래처럼하면 값 이미 있을수도 있음
            $product_to_be_updated->name = $request->product_name;
            $product_to_be_updated->price = $request->product_price;
            $product_to_be_updated->discounted_price = $request->product_discounted_price;
            $product_to_be_updated->stock = $request->product_stock;
            $product_to_be_updated->seller_id = auth()->user()->id; //필요없지않아?
            $product_to_be_updated->brand_id = auth()->user()->brand_id;
            $product_to_be_updated->category_id = $request->sub_category_id;
            $product_to_be_updated->status = $request->product_status;

            $product_to_be_updated->save();

            //여기 hasFile, validFile 말고 이거 쓴 이유 다시 생각해보기
            if ($request->product_image instanceof UploadedFile) {
                $request->file('product_image')->storeAs('public/product_image', $request->product_id.'.png');
                Cache::flush(); // update 한 새로운 이미지 가져오기 위해 캐시 삭제
                //php artisan config:cache 필요
            }

            $success_fail_status = 'success' ; //이것들 나중에 상수로 정의?

        } catch (QueryException $queryException) {
            $success_fail_status = 'query_fail';
            //use Illuminate\Database\QueryException; 쓰지 않으면 여기에 걸리지 않음
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

}
