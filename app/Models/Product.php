<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'sku',
        'category_id',
        'supplier_id',
        'cost_price',
        'sell_price',
        'stock_quantity',
        'low_stock_threshold',
        'image_path',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'sell_price' => 'decimal:2',
        'low_stock_threshold' => 'integer',
    ];

    // Returns a ready-to-use image URL, or null if no image was uploaded.
    public function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::disk('s3')->url($this->image_path) : null;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}