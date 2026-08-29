<?php

namespace App\Modules\Stock\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockLocation extends Model
{
    protected $table = 'stock_locations';
    protected $fillable = ['name', 'code', 'notes', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function balances(): HasMany
    {
        return $this->hasMany(StockBalance::class, 'location_id');
    }
}
