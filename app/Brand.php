<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'mall_brands';
    // 언더바 있을때는 Mall_brand 와 Mall_brand 중 어느 것을 관례라고 인식할지 몰라 테이블 지정해줌
    // 어차피 테이블 지정할거 모델이름 Brand 로 수정

    protected $fillable = ['name'];


    public $timestamps = false;

    public function sellers()
    {
        return $this->hasMany(Seller::class,'brand_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class,'brand_id');
    }

    public function brandProductDiscounts()
    {
        return $this->hasMany(BrandProductDiscount::class,'brand_id');
    }

    public function brandProductDiscount()
    {
        return $this->hasOne(BrandProductDiscount::class,'brand_id')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->orderByDesc('id');
    }

}
