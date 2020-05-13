<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'mall_products';

    protected $fillable = [
        'name', 'price', 'discounted_price', 'stock', 'seller_id', 'brand_id', 'category_id', 'status'
    ];

    //protected $with = ['brand.brandProductDiscount'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function updateLogs()
    {
        return $this->hasMany(UpdateLog::class, 'product_id')->orderByDesc('updated_at');
    }

    public function brandProductDiscount()
    {
        return $this->hasOne(BrandProductDiscount::class,'brand_id', 'brand_id')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->orderByDesc('id');
    }

    public function categoryProductDiscount()
    {
        return $this->hasOne(CategoryProductDiscount::class,'category_id', 'category_id')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->orderByDesc('id');
    }

    public function getProductImagePathAttribute()
    {
        return asset(Storage::url('product_image/'.$this->id.'.png'));
    }

    public function makeRandomKoreanProductName() {
        $product_name = [
            'adjectives' => ['작은', '큰', '유행 안타는', '가벼운', '가성비 좋은', '일상적인', '힙한'],
            'colors' => ['베이비핑크', '블랙', '네이비', '실버', '베이지', '로즈골드', '화이트골드', '카키'],
            'items' => ['스니커즈', '샤프', '노트북', '지갑', '슬랙스', '셔츠', '가디건', '메모지', '코트', '패딩', '손난로', '다이어리']
        ];

        return Arr::random($product_name['adjectives'])
            .' '. Arr::random($product_name['colors'])
            .' '. Arr::random($product_name['items']);
    }

    public function getDiscountedPrice() {

        /*브랜드할인율, 카테고리할인율 변수 정의*/
        $brandDiscount = 0;
        $categoryDiscount = 0;
        if ($this->brandProductDiscount != null && $this->price >= $this->brandProductDiscount->from_price && $this->brandProductDiscount->discount_percentage != 0) {
            $brandDiscount = $this->brandProductDiscount->discount_percentage;
        }
        if ($this->categoryProductDiscount != null && $this->price >= $this->categoryProductDiscount->from_price && $this->categoryProductDiscount->discount_percentage != 0) {
            $categoryDiscount = $this->categoryProductDiscount->discount_percentage;
        }

        /*브랜드할인율과 카테고리 할인율에 따른 할인가 계산*/
        //계산 위해 정가로 초기화
        $discountedPrice = $this->price;
        //브랜드할인율이 0이 아닐때
        if($brandDiscount != 0) {
            $discountedPrice = $discountedPrice - ($discountedPrice * ($brandDiscount/100)) ;
            $discountedPrice = round($discountedPrice/100)*100;
        }
        //카테고리할인율이 0이 아닐때
        if($categoryDiscount != 0) {
            $discountedPrice = $discountedPrice - ($discountedPrice * ($categoryDiscount/100)) ;
            $discountedPrice = round($discountedPrice/100)*100;
        }

        //할인이 없을때
        if ($brandDiscount == 0 && $categoryDiscount == 0) {
            $discountedPrice = $this->discounted_price;
        }


        return '브랜드 할인' . $brandDiscount . ', 카테고리할인' . $categoryDiscount . ', 최종 할인가' . $discountedPrice;

    }
}
