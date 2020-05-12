<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryProductDiscount extends Model
{
    protected $table = 'mall_category_products_discount';

    protected $fillable = ['category_id', 'from_price', 'discount_percentage', 'start_date', 'end_date'];

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id')
            ->where('price', '>=', $this->from_price);
    }

    public function getProductsCountAttr()
    {
        //return count($this->products);
        return $this->products()->count();
    }

}
