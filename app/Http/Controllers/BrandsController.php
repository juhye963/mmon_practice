<?php

namespace App\Http\Controllers;

use App\Brand;
use Illuminate\Http\Request;
//use Illuminate\Database\Eloquent\Model as Model;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('brands.select', ['brands' => $brands]);
        //gettype() 하면 object 반환
    }
}
