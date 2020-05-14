<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandDiscountExceptionProducts extends Model
{
    protected $table = 'mall_brand_discount_exception_products';

    protected $fillable = ['brand_discount_id', 'product_id'];

    public $timestamps = false;

}
