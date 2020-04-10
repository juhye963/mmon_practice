<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mall_Brand extends Model
{
    protected $table = 'mall_brands';
    // 언더바 있을때는 Mall_brand 와 Mall_brand 중 어느 것을 관례라고 인식할지 몰라 테이블 지정해줌

    protected $fillable = ['name'];
    // 왜 fillable 오타라고 인식..? 나중에 안먹나? 확인해보자



    public function mall_sellers()
    {
        return $this->hasMany(Mall_Seller::class,'brand_id');
    }

}
