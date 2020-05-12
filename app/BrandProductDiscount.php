<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandProductDiscount extends Model
{
    protected $table = 'mall_brand_products_discount';

    protected $fillable = ['brand_id', 'from_price', 'discount_percentage', 'start_date', 'end_date'];

    protected $with = ['brand'];

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function getTotalCountOfBrandDiscountTargetProducts () {
        $min_price_of_target = $this->from_price;
        $brand_id = $this->brand_id;

        $target_products_total_count = Brand::find($brand_id)->products->where('price', '>=', $min_price_of_target)->count();

        return $target_products_total_count;
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id')
            ->where('price', '>=', $this->from_price);
    }


}
