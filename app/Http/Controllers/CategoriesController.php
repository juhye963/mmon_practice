<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryProductDiscount;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{

    public function displaySubCategories(Request $request)
    {

        $categoryId = $request->input('category_pid');
        //dd($categoryId);
        $sub_categories = Category::where('pid', '=', $categoryId)->get();

        return response()->json([
            'sub_categories' => $sub_categories

        ]);
    }

    public function insertManyCategories() {

        $categories_data_set = [];
        $department = ["Books", "Movies", "Music", "Games", "Electronics", "Computers", "Home", "Garden", "Tools", "Grocery", "Health", "Beauty", "Toys",
            "Kids", "Baby", "Clothing", "Shoes", "Jewelry", "Sports", "Outdoors", "Automotive", "Industrial"];


        for ($i = 0; $i < 10; $i++) {
            $random_category_name = Arr::random($department).' & '.Arr::random($department);
            if (Category::where('name', '=', $random_category_name)->first() != null) {
                $i--;
                continue;
            }
            $categories_data_set[$i] = [
                'name' => $random_category_name,
                'pid' => 0
            ];
        }

        Category::insert($categories_data_set);
        $categories_data_set = [];

        for ($j = 0; $j < 190; $j++) {
            $random_category_name = Arr::random($department).' & '.Arr::random($department);
            if (Category::where('name', '=', $random_category_name)->first() != null) {
                $i--;
                continue;
            }
            $categories_data_set[$j] = [
                'name' => $random_category_name,
                'pid' => Category::where('pid', '=', 0)->get()->random()->id
            ];
        }
        Category::insert($categories_data_set);

        //insert 는 true false 반환
        //create 해당 모델 인스턴스 반환

        return $i . "+" . $j . " 개의 데이터 입력";

    }

    public function createCategoryDiscount () {
        $categories = Category::where('pid', '=', '0')->get();
        return view('categories.create-discount')->with([
            'categories' => $categories,
        ]);
    }

    public function storeCategoryDiscount(Request $request) {
        $request->validate([
            'category_id' => 'required|exists:mall_categories,id',
            'discount_target_min_price' => 'integer|min:0|max:1000000',
            'discount_percentage' => 'integer|min:0|max:99',
            'discount_start_date' => 'required|date|before_or_equal:discount_end_date',
            'discount_end_date' => 'required|date'
        ]);

        $parameters = $request->only(
            'category_id',
            'discount_target_min_price',
            'discount_percentage',
            'discount_start_date',
            'discount_end_date',
        );

        CategoryProductDiscount::create([
            'category_id' => $parameters['category_id'],
            'from_price' => $parameters['discount_target_min_price'],
            'discount_percentage' => $parameters['discount_percentage'],
            'start_date' => $parameters['discount_start_date'],
            'end_date' => $parameters['discount_end_date'],
        ])->save();

        return response()->json([]);

    }

    public function listCategoryDiscount() {
        $category_discount_lists = CategoryProductDiscount::all()->sortKeysDesc()->loadCount('products');

        return view('categories.discount-list')->with(['category_product_discount_lists' => $category_discount_lists]);

    }

    public function editCategoryDiscount($category_discount_id) {
        $category_discount_data = CategoryProductDiscount::find($category_discount_id);

        return view('categories.edit-discount')->with([
            'category_discount_data' => $category_discount_data,
        ]);
    }

    public function updateCategoryDiscount(Request $request) {
        $request->validate([
            "category_discount_id" => "required|exists:mall_category_products_discount,id",
            "discount_percentage" => "integer|min:0|max:99",
            "discount_target_min_price" => "integer|min:0|max:1000000",
            'discount_start_date' => 'required|date|before_or_equal:discount_end_date',
            'discount_end_date' => 'required|date'
        ]);

        $category_discount_update_data = $request->only(
            'category_discount_id',
            'discount_percentage',
            'discount_target_min_price',
            'discount_start_date',
            'discount_end_date',
            );

        CategoryProductDiscount::find($category_discount_update_data['category_discount_id'])->update([
            'from_price' => $category_discount_update_data['discount_target_min_price'],
            'discount_percentage' => $category_discount_update_data['discount_percentage'],
            'start_date' => $category_discount_update_data['discount_start_date'],
            'end_date' => $category_discount_update_data['discount_end_date']
        ]);

        return response()->json([]);
    }

    public function showTargetProductOfCategoryDiscount(Request $request) {

        $request->validate([
            'discount_target_category_id' => 'required|exists:mall_brands,id',
            'discount_target_min_price' => 'integer|min:0|max:1000000',
            //'discount_percentage' => 'integer|min:0|max:99'
        ]);


        $parameters['discount_target_category_id'] = $request->input('discount_target_category_id', '');
        $parameters['discount_target_min_price'] = $request->input('discount_target_min_price', 0);
        //$parameters['discount_percentage'] = $request->input('discount_percentage', 0);


        $targetProductsOfBrandDiscount = Product::where('category_id', $parameters['discount_target_category_id'])
            ->where('price', '>=', $parameters['discount_target_min_price'])
            ->orderBy('price')
            ->paginate(10);

        $targetProductsOfBrandDiscount->appends($parameters);

        return response()->json(['targetProducts' => $targetProductsOfBrandDiscount]);
    }

}
