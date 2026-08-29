<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $table = 'stock_movements';
    protected $fillable = [
        'stock_item_id', 'location_id', 'type', 'quantity', 'unit_cost', 'lot_number',
        'expires_at', 'reference_type', 'reference_id', 'notes', 'created_by', 'occurred_at',
    ];
    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:4',
        'expires_at' => 'date',
        'occurred_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }
}
