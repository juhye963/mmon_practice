<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public $timestamps = false;

    protected $table = 'mall_categories';

    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function parentCategory()
    {
        return $this->belongsTo(Category::class, 'pid', 'id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'pid', 'id');
    }

}
