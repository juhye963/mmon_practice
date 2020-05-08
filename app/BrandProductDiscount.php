<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandProductDiscount extends Model
{
    protected $table = 'mall_brand_products_discount';

    protected $fillable = ['brand_id', 'from_price', 'discount_percentage', 'start_date', 'end_date'];

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

}
