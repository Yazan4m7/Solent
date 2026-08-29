<?php

namespace App\Modules\Stock\Services;

use App\Modules\Stock\Models\StockBalance;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockLocation;
use App\Modules\Stock\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function move(
        StockItem $item,
        StockLocation $location,
        float $signedQuantity,
        string $type,
        array $meta = []
    ): StockMovement {
        if ($signedQuantity == 0.0) {
            throw ValidationException::withMessages(['quantity' => 'Quantity cannot be zero.']);
        }

        return DB::transaction(function () use ($item, $location, $signedQuantity, $type, $meta) {
            $balance = StockBalance::query()
                ->where('stock_item_id', $item->id)
                ->where('location_id', $location->id)
                ->lockForUpdate()
                ->first();

            if (!$balance) {
                $balance = StockBalance::create([
                    'stock_item_id' => $item->id,
                    'location_id' => $location->id,
                    'quantity' => 0,
                ]);
                $balance->refresh();
            }

            $newQuantity = (float) $balance->quantity + $signedQuantity;
            if ($newQuantity < 0) {
                throw ValidationException::withMessages([
                    'quantity' => "Not enough stock for {$item->name} at {$location->name}.",
                ]);
            }

            $balance->update(['quantity' => $newQuantity]);

            return StockMovement::create([
                'stock_item_id' => $item->id,
                'location_id' => $location->id,
                'type' => $type,
                'quantity' => $signedQuantity,
                'unit_cost' => $meta['unit_cost'] ?? null,
                'lot_number' => $meta['lot_number'] ?? null,
                'expires_at' => $meta['expires_at'] ?? null,
                'reference_type' => $meta['reference_type'] ?? null,
                'reference_id' => $meta['reference_id'] ?? null,
                'notes' => $meta['notes'] ?? null,
                'created_by' => $meta['created_by'] ?? auth()->id(),
                'occurred_at' => $meta['occurred_at'] ?? now(),
            ]);
        });
    }

    public function consume(
        int $stockItemId,
        float $quantity,
        int $locationId,
        ?int $referenceId = null,
        string $referenceType = 'job',
        ?string $notes = null
    ): StockMovement {
        $item = StockItem::findOrFail($stockItemId);
        $location = StockLocation::findOrFail($locationId);

        return $this->move($item, $location, -abs($quantity), 'job_usage', [
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'notes' => $notes,
        ]);
    }
}
