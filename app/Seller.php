<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Seller extends Authenticatable
{
    //기존의 User 모델을 Mall_Seller로 바꾸려함
    //그렇다면 User가 어떻게 생겼고 어디에서 얘를 바라보고(참조하고) 있는지 잘 찾아봐야함(주의!!)
    //기존 User 모델은 Authenticatable을 상속받는데 일반모델로 만드니까(Model 상속받는) 문제 터졌음
    // \config\auth.php 수정해줌

    protected $table = 'mall_sellers';

    protected $fillable = [
        'name', 'email', 'password', 'brand_id'
    ];

    //protected $hidden = 'password';
    //ErrorException  : count(): Parameter must be an array or an object that implements Countable
    //  at D:\workspace\board-test\vendor\laravel\framework\src\Illuminate\Database\Eloquent\Concerns\HasAttributes.php:299
    //hidden 설정해놓으면 위의 오류남 (가시성, 비가시성을 설정)

    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class,'seller_id');
    }

    public function updateLogs()
    {
        return $this->hasMany(UpdateLog::class,'seller_id');
    }
}
