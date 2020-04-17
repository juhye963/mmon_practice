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
        $parms['prds_status'] = $request->input('prds_status', '');
        $parms['start_date'] = $request->input('start_date', '');
        $parms['end_date'] = $request->input('end_date', '');

        //검색어 있을때는 검색유형 필수
        if ( $parms['search_word'] != '' ) {
            $request->validate([
                'search_type' => 'required'
                //exists 이용하든 뭘하든 내가 설정한 검색유형중에 있는 값인지 확인하기
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

        if ( $parms['prds_status'] != '') {
            $products = $products->where('status', '=', $parms['prds_status']);
        }

        //현재보다 미래의 날짜는 입력할 수 없음 (날짜 찍히는거 보고 잘 비교하기)
        //날짜 한쪽이 입력되면 다른 한쪽도 필요함
        if ( $parms['start_date'] != '' || $parms['end_date'] != '' ) {
            $request->validate([
//                'start_date' => 'required|lte:date(\'Y-m-d\')',
//                'end_date' => 'required|lte:date(\'Y-m-d\')'
                'start_date' => 'required',
                'end_date' => 'required'
            ]);

            //$products = $products->whereBetween('created_at', [$parms['start_date'], $parms['start_date']]);
            $products = $products->where('created_at', '>=', $parms['start_date'])
                ->where('created_at', '<=', $parms['end_date']);
            //종료일은 포함되지 않음. 16일이 종료일이면 16일 상품은 안나옴
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

        $sorts = [
            '최근 등록 순' => 'recent',
            '낮은 가격 순' => 'price_asc',
            '높은 가격 순' => 'price_desc',
            '상품명 순' => 'prds_nm_asc'
        ];

        $prds_status = [
            '판매중' => 'selling',
            '판매중지' => 'stop_selling',
            '일시품절' => 'sold_out'
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
            '등록일',
            '상태',
            '상품삭제',
        ];


        return view('products.index')->with([
            'products' => $products,
            'parms' => $parms,
            'search_types' => $search_types,
            'sorts' => $sorts,
            'prds_theads' => $prds_theads,
            'prds_status' => $prds_status
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
