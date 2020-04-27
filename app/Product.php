<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'mall_products';

    protected $fillable = [
        'name', 'filename', 'price', 'discount', 'stock', 'seller_id', 'brand_id', 'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /*public function getProductImageNameAttribute()
    {
        return "{ $this->id }.png";
    }*/

    public function getProductImagePathAttribute()
    {
        return asset(Storage::url('product_image/'.$this->id.'.png'));
    }
}
