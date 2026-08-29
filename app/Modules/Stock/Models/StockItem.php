<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    protected $table = 'stock_items';

    protected $fillable = [
        'sku', 'name', 'category', 'unit', 'minimum_stock', 'target_stock',
        'default_unit_cost', 'description', 'is_active',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:3',
        'target_stock' => 'decimal:3',
        'default_unit_cost' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function balances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'stock_item_id');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'stock_item_id');
    }

    public function purchaseLines(): HasMany
    {
        return $this->hasMany(StockPurchaseLine::class, 'stock_item_id');
    }
}
