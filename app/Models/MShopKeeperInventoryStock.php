<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model cho bảng mshopkeeper_inventory_stocks
 * Lưu trữ thông tin tồn kho theo chi nhánh từ MShopKeeper API
 */
class MShopKeeperInventoryStock extends Model
{
    use HasFactory;

    protected $table = 'mshopkeeper_inventory_stocks';

    protected $fillable = [
        'inventory_item_id',
        'product_mshopkeeper_id',
        'product_code',
        'product_name',
        'branch_mshopkeeper_id',
        'branch_name',
        'on_hand',
        'ordered',
        'selling_price',
        'last_synced_at',
        'sync_status',
        'sync_error',
        'raw_data',
    ];

    protected $casts = [
        'on_hand' => 'integer',
        'ordered' => 'integer',
        'selling_price' => 'decimal:2',
        'last_synced_at' => 'datetime',
        'raw_data' => 'array',
    ];

    /**
     * Relationships
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(MShopKeeperInventoryItem::class, 'inventory_item_id');
    }

    /**
     * Scopes
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    public function scopeSyncErrors($query)
    {
        return $query->where('sync_status', 'error');
    }

    public function scopePendingSync($query)
    {
        return $query->where('sync_status', 'pending');
    }

    public function scopeInStock($query)
    {
        return $query->where('on_hand', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('on_hand', '<=', 0);
    }

    public function scopeByBranch($query, string $branchId)
    {
        return $query->where('branch_mshopkeeper_id', $branchId);
    }

    public function scopeLowStock($query, int $threshold = 10)
    {
        return $query->where('on_hand', '<=', $threshold)->where('on_hand', '>', 0);
    }

    /**
     * Accessors
     */
    public function getFormattedSellingPriceAttribute(): string
    {
        return number_format($this->selling_price) . ' VND';
    }

    public function getStockStatusAttribute(): string
    {
        return match (true) {
            $this->on_hand > 100 => 'Nhiều',
            $this->on_hand > 10 => 'Vừa',
            $this->on_hand > 0 => 'Ít',
            default => 'Hết hàng'
        };
    }

    public function getStockStatusColorAttribute(): string
    {
        return match (true) {
            $this->on_hand > 100 => 'success',
            $this->on_hand > 10 => 'info',
            $this->on_hand > 0 => 'warning',
            default => 'danger'
        };
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->on_hand - $this->ordered);
    }

    /**
     * Static methods
     */
    public static function getSyncStats(): array
    {
        $lastSyncRecord = static::synced()->orderBy('last_synced_at', 'desc')->first();
        
        return [
            'total' => static::count(),
            'synced' => static::synced()->count(),
            'errors' => static::syncErrors()->count(),
            'pending' => static::pendingSync()->count(),
            'in_stock' => static::inStock()->count(),
            'out_of_stock' => static::outOfStock()->count(),
            'low_stock' => static::lowStock()->count(),
            'last_sync' => $lastSyncRecord ? $lastSyncRecord->last_synced_at : null,
            'total_stock_value' => static::selectRaw('SUM(on_hand * selling_price)')->value('SUM(on_hand * selling_price)') ?? 0,
            'total_on_hand' => static::sum('on_hand'),
            'total_ordered' => static::sum('ordered'),
        ];
    }

    /**
     * Sync tracking methods
     */
    public function needsSync(int $minutesThreshold = 30): bool
    {
        if (!$this->last_synced_at) {
            return true;
        }

        return $this->last_synced_at->diffInMinutes(now()) > $minutesThreshold;
    }

    public function markAsSynced(array $rawData = []): void
    {
        $this->update([
            'last_synced_at' => now(),
            'sync_status' => 'synced',
            'sync_error' => null,
            'raw_data' => $rawData,
        ]);
    }

    public function markAsError(string $error): void
    {
        $this->update([
            'sync_status' => 'error',
            'sync_error' => $error,
        ]);
    }

    /**
     * Normalize API data structure theo response format
     */
    public static function normalizeApiData(array $data): array
    {
        return [
            'product_mshopkeeper_id' => $data['ProductId'] ?? null,
            'product_code' => $data['ProductCode'] ?? '',
            'product_name' => $data['ProductName'] ?? '',
            'branch_mshopkeeper_id' => $data['BranchId'] ?? null,
            'branch_name' => $data['BranchName'] ?? '',
            'on_hand' => (int) ($data['OnHand'] ?? 0),
            'ordered' => (int) ($data['Ordered'] ?? 0),
            'selling_price' => (float) ($data['SellingPrice'] ?? 0),
        ];
    }

    /**
     * Update parent inventory item total stock after changes
     */
    protected static function booted()
    {
        static::saved(function ($stock) {
            if ($stock->inventoryItem) {
                $stock->inventoryItem->updateTotalStock();
            }
        });

        static::deleted(function ($stock) {
            if ($stock->inventoryItem) {
                $stock->inventoryItem->updateTotalStock();
            }
        });
    }
}
