<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\Product;
use App\Seller;
use Illuminate\Http\Request;
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
            'discount' => 'required|min:1|max:1000000|lte:price',
            'amount' => 'required|min:0|max:1000',
            'category_id' => 'required|exists:mall_categories,id'
        ]);

        //required 오타나면 나오는 에러
        // Method Illuminate\Validation\Validator::validateRequire does not exist.

        $product = new Product();

        $product->name = $request->name;
        $product->price = $request->price;
        $product->discount = $request->discount;
        $product->amount = $request->amount;
        $product->seller_id = auth()->user()->id;
        $product->brand_id = auth()->user()->brand_id;
        $product->category_id = $request->category_id;

        $product->save();

        if($request->hasFile('product_image')){
            $path = $request->file('product_image')->storeAs('public/product_image',$product->id.'.png');
            //확장자 jpg, png 설정하면 그대로 저장되긴하는데.. 이래도 되나?
            $product->filename = $path;
            $product->save();
        }
        //Unable to guess the MIME type as no guessers are available (have you enable the php_fileinfo extension?).
        //php.ini 에서 해당 extension enable 하면 해결됨
        // storage/app/public 에 저장됨

        return redirect( route('home'));
    }

    public function index()
    {
        $seller = auth()->user();
        $products = $seller->products()->with('brand','category')->paginate(5);
        //https://laravel.kr/docs/6.x/eloquent-relationships#eager-loading 참고
        //https://stackoverflow.com/questions/48732007/laravel-eloquent-relation-for-getting-user-name-for-a-specific-id

        return view('products.index')->with([
            'products' => $products
        ]);
    }
}
