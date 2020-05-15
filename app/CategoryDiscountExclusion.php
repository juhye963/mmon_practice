<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryDiscountExclusion extends Model
{
    protected $table = 'mall_category_discount_exclusions';

    protected $fillable = ['category_discount_id', 'product_id'];

    public $timestamps = false;

    public function product() {
        $this->belongsTo(Product::class, 'product_id','id');
    }

}
