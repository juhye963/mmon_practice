<?php

namespace App\Http\Controllers;

use App\Brand;
use App\BrandDiscountExclusion;
use App\BrandProductDiscount;
use App\CategoryDiscountExclusion;
use App\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function listBrandDiscounts() {

        $brand_product_discount_lists = BrandProductDiscount::all()->sortKeysDesc()->loadCount('products');
        return view('brands.discount-list', ['brand_product_discount_lists' => $brand_product_discount_lists]);
    }

    public function createBrandDiscount() {
        $brands = Brand::all();

        return view('brands.create-discount', ['brands' => $brands]);
    }

    public function storeBrandDiscount(Request $request) {

        $validatedData = $request->validate([
            'discount_target_brand_id' => 'required|exists:mall_brands,id',
            'discount_percentage' => 'required|integer|min:0|max:99',
            'discount_target_min_price' => 'required|integer|min:0|max:1000000',
            'discount_start_date' => 'required|date|before_or_equal:discount_end_date',
            'discount_end_date' => 'required|date'
        ]);

        $brand_discount_data = $request->only(
            'discount_target_brand_id',
            'discount_percentage',
            'discount_target_min_price',
            'discount_start_date',
            'discount_end_date');

        $brand_discount = new BrandProductDiscount();

        $brand_discount->brand_id = $brand_discount_data['discount_target_brand_id'];
        $brand_discount->from_price = $brand_discount_data['discount_target_min_price'];
        $brand_discount->discount_percentage = $brand_discount_data['discount_percentage'];
        $brand_discount->start_date = $brand_discount_data['discount_start_date'];
        $brand_discount->end_date = $brand_discount_data['discount_end_date'];

        $brand_discount->save();

        $this_brand_discount_id = BrandProductDiscount::orderByDesc('id')->first();

        return response()->json(['thisBrandDiscountId' => $this_brand_discount_id]);
    }

    public function showTargetProductOfBrandDiscount(Request $request) {

        $request->validate([
            'discount_target_brand_id' => 'required|exists:mall_brands,id',
            'discount_target_min_price' => 'integer|min:0|max:1000000',
            'discount_percentage' => 'integer|min:0|max:99'
        ]);

        $parameters['discount_target_brand_id'] = $request->input('discount_target_brand_id', '');
        $parameters['discount_target_min_price'] = $request->input('discount_target_min_price', 0);
        $parameters['discount_percentage'] = $request->input('discount_percentage', 0);

        $targetProductsOfBrandDiscount = Product::with('categoryProductDiscount')
            ->where('brand_id', $parameters['discount_target_brand_id'])
            ->where('price', '>=', $parameters['discount_target_min_price'])
            ->orderBy('price')
            ->paginate(10);

        $targetProductsOfBrandDiscount;

        return response()->json(['targetProducts' => $targetProductsOfBrandDiscount]);
    }

    public function editBrandDiscount ($brand_discount_id) {

        $brand_discount_data = BrandProductDiscount::find($brand_discount_id);

        return view('brands.edit-discount')->with([
            'brand_discount_data' => $brand_discount_data,
        ]);
    }

    public function updateBrandDiscount (Request $request) {
        $validatedData = $request->validate([
            'brand_discount_id' => 'required|exists:mall_brand_products_discount,id',
            'discount_percentage' => 'required|numeric|min:0|max:99',
            'discount_target_min_price' => 'required|numeric|min:0|max:1000000',
            'discount_start_date' => 'required|date|before_or_equal:discount_end_date',
            'discount_end_date' => 'required|date'
        ]);

        $brand_discount_update_data = $request->only(
            'brand_discount_id',
            'discount_percentage',
            'discount_target_min_price',
            'discount_start_date',
            'discount_end_date');

        BrandProductDiscount::find($brand_discount_update_data['brand_discount_id'])->update([
            'from_price' => $brand_discount_update_data['discount_target_min_price'],
            'discount_percentage' => $brand_discount_update_data['discount_percentage'],
            'start_date' => $brand_discount_update_data['discount_start_date'],
            'end_date' => $brand_discount_update_data['discount_end_date']
        ]);

        return response()->json([]);
    }

    public function createBrandDiscountExcludedProducts () {
        return view('brands.create-discount-exclusions');
    }

    public function displaySearchedProductsForDiscountExclusions(Request $request) {

        $product_id_set = $request->input('brand_discount_exclusion_target_product_id', []);

        //유효성검사

        $request->validate([
            'brand_discount_exclusion_target_product_id' => 'array|between:1,5000'
        ]);

        for ($i = 0; $i < count($product_id_set); $i++) {
            $request->validate([
                'brand_discount_exclusion_target_product_id.'.$i => 'numeric'
            ]);
        }

        //상품 정보 가져오기
        $searched_product = Product::whereIn('id', $product_id_set)->paginate(10)->appends($product_id_set);

        return response()->json(['searchedProduct' => $searched_product]);
    }

    public function storeBrandDiscountExcludedProducts (Request $request) {
        $product_id_set = $request->input('product_id_set', []);
        $brand_discount_id = $request->input('brand_discount_id');
        $brandDiscountExcludedProducts = [];


        //넘어온 상품아이디가 상품테이블에 존재하는지 확인
        //중복상품이 있다면 중복 제거
        for ($i = 0; $i < count($product_id_set); $i++){
            if(Product::where('id', $product_id_set[$i])->exists()
                && in_array($product_id_set[$i], $brandDiscountExcludedProducts) == false) {
                $brandDiscountExcludedProducts[$i] =  [
                    'product_id' => $product_id_set[$i],
                    'brand_discount_id' => $brand_discount_id
                ];
            }
        }

        //기존 제외상품 있는데 재등록한 경우 기존제외상품은 지움
        if (empty($brandDiscountExcludedProducts) == false) {
            BrandDiscountExclusion::where('brand_discount_id', '=', $brand_discount_id)->delete();
        }


        $result = BrandDiscountExclusion::insert($brandDiscountExcludedProducts);

        return response()->json(['result' => $result]);
    }

    public function storeCategoryDiscountExcludedProducts (Request $request) {
        $product_id_set = $request->input('product_id_set', []);
        $category_discount_id = $request->input('category_discount_id');
        $categoryDiscountExcludedProducts = [];

        //넘어온 상품아이디가 상품테이블에 존재하는지 확인
        //중복상품이 있다면 중복 제거
        for ($i = 0; $i < count($product_id_set); $i++){
            if(Product::where('id', $product_id_set[$i])->exists()
                && in_array($product_id_set[$i], $categoryDiscountExcludedProducts) == false) {
                $categoryDiscountExcludedProducts[$i] =  [
                    'product_id' => $product_id_set[$i],
                    'category_discount_id' => $category_discount_id
                ];
            }
        }

        if (empty($categoryDiscountExcludedProducts) == false) {
            CategoryDiscountExclusion::where('category_discount_id', '=', $category_discount_id)->delete();
        }

        $result = CategoryDiscountExclusion::insert($categoryDiscountExcludedProducts);

        return response()->json(['result' => $result]);

    }

}
