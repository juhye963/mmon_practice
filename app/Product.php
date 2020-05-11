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

    protected $with = ['brand.brandProductDiscount'];

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
}
