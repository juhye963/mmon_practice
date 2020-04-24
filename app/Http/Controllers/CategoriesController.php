<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

}
