<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'unit_cost',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isStockIn(): bool
    {
        return $this->type === 'in';
    }

    // One-line helper so every stock change (POS, void, stock-in)
    // records who/what/when in exactly the same shape.
    public static function record(
        int $productId,
        string $type,
        int $quantity,
        ?float $unitCost,
        ?int $userId,
        ?string $notes = null
    ): self {
        $movement = new self;
        $movement->product_id = $productId;
        $movement->type = $type;
        $movement->quantity = $quantity;
        $movement->unit_cost = $unitCost;
        $movement->user_id = $userId;
        $movement->notes = $notes;
        $movement->save();

        return $movement;
    }
}
