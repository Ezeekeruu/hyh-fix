<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    // Built from config only (never boots the S3 client) so pages render
    // even when AWS credentials are absent; falls back to local storage.
    public function getImageUrlAttribute()
    {
        if (empty($this->image_path)) {
            return null;
        }

        $base = rtrim((string) config('filesystems.disks.s3.url'), '/');

        return $base !== '' ? $base.'/'.$this->image_path : asset('storage/'.$this->image_path);
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