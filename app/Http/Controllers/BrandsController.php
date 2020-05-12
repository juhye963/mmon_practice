<?php

namespace App\Http\Controllers;

use App\Brand;
use App\BrandProductDiscount;
use App\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use function MongoDB\BSON\toJSON;

//use Illuminate\Database\Eloquent\Model as Model;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('brands.select', ['brands' => $brands]);
        //gettype() 하면 object 반환
    }

    public function insertManyBrands() {


        $brands_name_prefix = ['BMW', 'McDonald\'s', 'Disney', 'Intel', 'Facebook', 'Cisco', 'Oracle', 'Nike', 'Louis Vuitton', 'H&M'];
        $brands_name_middle = ['Honda', 'SAP', 'Pepsi', 'Gillette', 'American Express', 'IKEA', 'Zara', 'Pampers', 'UPS', 'Budweiser'];
        $brands_name_suffix = ['Apple', 'Google', 'Coca-Cola', 'Microsoft', 'Toyota', 'IBM', 'Samsung', 'Amazon', 'Mercedes-Benz', 'GE'];

        $brands_data_set = [];

        for ($i = 0; $i < 200; $i++) {
            $random_brand_name = Arr::random($brands_name_prefix)
                . ' & ' . Arr::random($brands_name_middle)
                . ' & ' . Arr::random($brands_name_suffix);
            if (in_array($random_brand_name, $brands_data_set)) {
                $i--;
                continue;
            }
            $brands_data_set[$i] = [
                'name' => $random_brand_name
            ];
        }

        Brand::insert($brands_data_set);

        return $i . "개의 브랜드 입력";
    }

    public function listBrandDiscounts() {

        /*$brand_product_discount_lists = BrandProductDiscount::withCount(['products' => function (Builder $query) {
            $query->where('price', '=>', );
        }])->get();*/
        $brand_product_discount_lists = BrandProductDiscount::all()->loadCount('products');
        return view('brands.discount-list', ['brand_product_discount_lists' => $brand_product_discount_lists]);
    }

    public function createBrandDiscount() {
        $brands = Brand::all();

        return view('brands.create-discount', ['brands' => $brands]);
    }

    public function storeBrandDiscount(Request $request) {

        $request->input('discount_target_min_price', '0');

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

        return response()->json([]);
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

        $targetProductsOfBrandDiscount = Product::where('brand_id', $parameters['discount_target_brand_id'])
            ->where('price', '>=', $parameters['discount_target_min_price'])
            ->orderBy('price')
            ->paginate(10);
        $targetProductsOfBrandDiscount->appends($parameters);

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

}
