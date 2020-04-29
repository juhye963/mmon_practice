<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function select()
    {
        $categories = Category::where('pid', '=', null);
        return view('categories.select', ['categories' => $categories]);
    }

    public function displaySubCategories(Request $request)
    {
        $categoryId = $request->input('category_pid');
        //dd($categoryId);
        $sub_categories = Category::where('pid', '=', $categoryId)->get();

        return response()->json([
            'sub_categories' => $sub_categories

        ]);
    }

    /*public function index()
    {
        $categories = Category::all();
        $categories = $categories->where('pid', '=', null);

        return view('categories.navigator', ['categories' => $categories]);
    }*/

    public function insertManyCategories() {

        for ($i = 0; $i < 3; $i++) {
            Category::insert([
                'name' => Str::random(6),
                'pid' => 0
            ]);
        }

        for ($i = 0; $i < 5; $i++) {
            Category::insert([
                'name' => Str::random(6),
                'pid' => Category::where('pid', '=', 0)->get()->random()->id
            ]);
        }

        //insert는 true false 반환
        //create 해당 모델 인스턴스 반환

    }
}
