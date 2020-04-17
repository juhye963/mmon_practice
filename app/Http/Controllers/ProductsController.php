<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Product;
use App\Seller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function create()
    {
        $categories = Category::all();

        return view('products.create', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|min:1|max:1000000',
            'discounted_price' => 'required|min:1|max:1000000|lte:price',
            'amount' => 'required|min:0|max:1000',
            'category_id' => 'required|exists:mall_categories,id'
        ]);

        //required 오타나면 나오는 에러
        // Method Illuminate\Validation\Validator::validateRequire does not exist.

        $product = new Product();

        $product->name = $request->name;
        $product->price = $request->price;
        $product->discounted_price = $request->discounted_price;
        $product->amount = $request->amount;
        $product->seller_id = auth()->user()->id;
        $product->brand_id = auth()->user()->brand_id;
        $product->category_id = $request->category_id;

        $product->save();

        if($request->hasFile('product_image')){
            $path = $request->file('product_image')->storeAs('public/product_image', $product->id.'.png');
            //확장자 jpg, png 설정하면 그대로 저장되긴하는데.. 이래도 되나?
        }
        //Unable to guess the MIME type as no guessers are available (have you enable the php_fileinfo extension?).
        //php.ini 에서 해당 extension enable 하면 해결됨
        // storage/app/public 에 저장됨

        return redirect(route('home'));
    }

    public function index(Request $request)
    {
        $products = Product::with('brand','category','seller');
        $parms = $request->all();
        $parms['search_type'] = $request->input('search_type', '');
        $parms['search_word'] = $request->input('search_word', '');
        $parms['sort'] = $request->input('sort', '');

        //검색어 있을때는 검색유형 필수
        if ( $parms['search_word'] != '' ) {
            $request->validate([
                'search_type' => 'required|max:255'
            ]);
        }

        //검색키워드로 찾기
        //상품명으로 검색 아닐시에는 검색유형(relation)에서 name으로 검색하게됨
        if ( $parms['search_type'] == 'prds_nm' ) {
            //더 좋은 방법 있을듯(계속 생각해보기)
            $products = $products->where('name', 'LIKE', '%' . $parms['search_word'] . '%');
        } elseif ( $parms['search_type'] == 'seller' || $parms['search_type'] == 'brand') {
            $products = $products->whereHas($parms['search_type'], function (Builder $query) use ($parms) {
                $query->where('name', 'LIKE', '%' . $parms['search_word'] . '%');
            });
        }

        //정렬조건 붙이기
        if ( $parms['sort'] != '') {
            switch ( $parms['sort'] ) {
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
        }

        $products = $products->paginate(5);

        $search_types = [
            '상품명' => 'prds_nm',
            '판매자 이름' => 'seller',
            '브랜드명' => 'brand'
        ];

        /*$search_types = [
            'prds_nm' => '상품명',
            'seller' => '판매자 이름',
            'brand' => '브랜드명'
        ];

        $sorts = [
            'recent' => '최근 등록 순',
            'price_asc' => '낮은 가격 순',
            'price_desc' => '높은 가격 순',
            'prds_nm_asc' => '상품명 순'
        ];*/

        $sorts = [
            '최근 등록 순' => 'recent',
            '낮은 가격 순' => 'price_asc',
            '높은 가격 순' => 'price_desc',
            '상품명 순' => 'prds_nm_asc'
        ];

        $prds_theads = [
            '상품번호',
            '상품명',
            '가격',
            '할인가',
            '재고',
            '브랜드',
            '카테고리',
            '등록자',
            '상품삭제',
        ];


        return view('products.index')->with([
            'products' => $products,
            'parms' => $parms,
            'search_types' => $search_types,
            'sorts' => $sorts,
            'prds_theads' => $prds_theads
        ]);
    }

    public function destroy($product_id)
    {
        $current_seller_id = auth()->user()->id;
        $product = Product::find($product_id);
        $product_seller_id = $product->seller->id;

        if ($current_seller_id == $product_seller_id) {
            $product->delete();
        } else {
            session()->flash('상품을 삭제할 권한이 없습니다.',false);
            //나중에는 삭제권한없으면 버튼 보이지도 않게 바꾸기
            //return '삭제권한 없다고.. 왜 플래시 안뜨냐고ㅠㅠㅠ';
        }

        return redirect(route('products.index'));
        //https://laravel.kr/docs/6.x/controllers#defining-controllers 참고
    }

}
