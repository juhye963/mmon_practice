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
            $path = $request->file('product_image')->storeAs('public/product_image',$product->id.'.png');
            //확장자 jpg, png 설정하면 그대로 저장되긴하는데.. 이래도 되나?
        }
        //Unable to guess the MIME type as no guessers are available (have you enable the php_fileinfo extension?).
        //php.ini 에서 해당 extension enable 하면 해결됨
        // storage/app/public 에 저장됨

        return redirect(route('home'));
    }

    public function index(Request $request)
    {
        /* 검색조건 */
        if ($request->has('prds_nm')) {
            $srch_key_org = $request->prds_nm;
            $srch_key = '%'.$request->prds_nm.'%';
            $query = Product::with('brand','category','seller')
                ->where('name','like',$srch_key);

        } elseif ($request->has('seller_nm')) {

            //dd($request->seller_nm);

            $srch_key_org = $request->seller_nm;
            $srch_key = '%'.$request->seller_nm.'%';

            $query = Product::with('brand','category','seller')
                ->whereHas('seller', function ( Builder $query) use ($srch_key) {
                    $query->where('name', 'like', $srch_key);
                });

            //use Illuminate\Database\Eloquent\Builder; 여기에서 Eloquent 대신 Query를 가져오면 에러남
            //https://laravel.kr/docs/6.x/eloquent-relationships#querying-relationship-existence 참고

        } else {
            $srch_key_org = 'none';
            $query = Product::with('brand','category','seller');
        }

        //dd($srch_key_org);

        //https://laravel.kr/docs/6.x/eloquent-relationships#eager-loading 참고
        //https://stackoverflow.com/questions/48732007/laravel-eloquent-relation-for-getting-user-name-for-a-specific-id


        /*정렬조건*/
        if ($request->has('sort')) {
            $sort = $request->sort;
            //정렬조건과 검색키워드 유지하면서 페이징하기 위한 변수 custom
            if ($request->has('prds_nm')) {
                $custom = '?prds_nm='.$srch_key_org.'&seller_nm=&sort='.$sort;
            } else if ($request->has('seller_nm')) {
                //dd($srch_key_org);
                $custom = '?prds_nm=&seller_nm='.$srch_key_org.'&sort='.$sort;
                //dd($srch_key_org);
            } else {
                $custom = '?prds_nm=&seller_nm=&sort='.$sort;
            }

            //정렬조건따른 쿼리문
            switch ($sort) {
                case 'recent':
                    $query_sort = $query->orderByDesc('updated_at');
                    break;
                case 'price_asc' :
                    $query_sort = $query->orderBy('price');
                    break;
                case 'price_desc' :
                    $query_sort = $query->orderByDesc('price');
                    break;
                case 'prds_name':
                    $query_sort = $query->orderBy('name');
                    break;
            }
        } else {
            $sort = 'none';
            $query_sort = $query;
        }

        //위에서 생성한 custom 변수가 있으면 같이 페이징. 없으면 그냥 페이징
        if ( $request->has('prds_nm') == false
            && $request->has('seller_nm') == false
            && $request->has('sort') == false ) {
            $products = $query_sort->paginate(5);
        } else {
            $products = $query_sort->paginate(5)->withPath($custom);
        }

        return view('products.index')->with([
            'products' => $products,
            'srch_key_org' => $srch_key_org,
            'sort' => $sort
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

    public function search()
    {

    }
}
