<?php

namespace App\Http\Controllers;

use App\Category;
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
}
