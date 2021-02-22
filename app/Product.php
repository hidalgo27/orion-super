<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $guarded = [];

    protected $table = "products";
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    public function photos()
    {
        return $this->hasMany(ProductPhoto::class, 'product_id');
    }
    public function order_product()
    {
        return $this->belongsTo(OrderProduct::class, 'product_id');
    }
}
