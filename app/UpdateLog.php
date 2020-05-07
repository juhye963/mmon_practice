<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpdateLog extends Model
{
    protected $table = 'mall_product_update_logs';

    protected $fillable = ['seller_id', 'ip_address', 'log_description', 'updated_at'];

    public $timestamps = false;

    public function seller()
    {
        return $this->belongsTo(Seller::class,'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

}
