<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Mall_Seller extends Authenticatable
{
    //기존의 User 모델을 Mall_Seller로 바꾸려함
    //그렇다면 User가 어떻게 생겼고 어디에서 얘를 바라보고(참조하고) 있는지 잘 찾아봐야함(주의!!)
    //기존 User 모델은 Authenticatable을 상속받는데 일반모델로 만드니까(Model 상속받는) 문제 터졌음

    protected $table = 'mall_sellers';
    // 언더바 있을때는 Mall_Seller 와 Mall_seller 중 어느 것을 관례라고 인식할지 몰라 테이블 지정해줌

    protected $fillable = [
        'name', 'email', 'password', 'brand_id'
    ];
    // 왜 fillable 오타라고 인식..? 나중에 안먹나? 확인해보자

    protected $hidden = 'password';

    public function mall_brand()
    {
        return $this->belongsTo(Mall_Brand::class,'brand_id');
    }


}
