<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockPurchase extends Model
{
    protected $table = 'stock_purchases';
    protected $fillable = ['supplier_id', 'reference_no', 'purchased_at', 'notes', 'created_by'];
    protected $casts = ['purchased_at' => 'date'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(StockSupplier::class, 'supplier_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(StockPurchaseLine::class, 'purchase_id');
    }
}
