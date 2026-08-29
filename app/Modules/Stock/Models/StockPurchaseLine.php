<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPurchaseLine extends Model
{
    protected $table = 'stock_purchase_lines';
    protected $fillable = [
        'purchase_id', 'stock_item_id', 'location_id', 'quantity', 'unit_cost',
        'lot_number', 'expires_at',
    ];
    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:4',
        'expires_at' => 'date',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(StockPurchase::class, 'purchase_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }
}
