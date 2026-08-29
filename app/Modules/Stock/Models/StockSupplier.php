<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockSupplier extends Model
{
    protected $table = 'stock_suppliers';
    protected $fillable = ['name', 'contact_person', 'phone', 'email', 'notes', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function purchases(): HasMany
    {
        return $this->hasMany(StockPurchase::class, 'supplier_id');
    }
}
