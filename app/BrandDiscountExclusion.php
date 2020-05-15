<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandDiscountExclusion extends Model
{
    protected $table = 'mall_brand_discount_exclusions';

    protected $fillable = ['brand_discount_id', 'product_id'];

    public $timestamps = false;

    public function product() {
        $this->belongsTo(Product::class, 'product_id','id');
    }

}
