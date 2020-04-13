<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Seller;
use Illuminate\Http\Request;

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
            'price' => 'required|max:1000000',
            'discount' => 'required|max:100',
            'amount' => 'required|max:1000'
        ]);

        //required 오타나면 나오는 에러
        // Method Illuminate\Validation\Validator::validateRequire does not exist.

        $product = new Product();

        if($request->hasFile('product_image')){
            $path = $request->file('product_image')->store('public/product_image');
            $product->filename = $path;
        }
        //Unable to guess the MIME type as no guessers are available (have you enable the php_fileinfo extension?).
        //php.ini 에서 해당 extension enable 하면 해결됨

        $product->name = $request->name;
        $product->price = $request->price;
        $product->discount = ($request->discount)/100;
        $product->amount = $request->amount;
        $product->seller_id = auth()->user()->id;
        $product->brand_id = auth()->user()->brand_id;
        $product->category_id = $request->category_id;

        $product->save();

        return redirect( route('home'));
    }

    public function show()
    {
        $current_seller_id = auth()->user()->id;
        $seller = Seller::find($current_seller_id);
        $product_cnt = $seller->products()->count();
        $products = $seller->products()->get();

        return view('products.show')->with([
            'product_cnt' => $product_cnt,
            'products' => $products
        ]);
    }
}
