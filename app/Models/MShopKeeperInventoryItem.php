<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model cho bảng mshopkeeper_inventory_items
 * Lưu trữ dữ liệu hàng hóa từ MShopKeeper API
 */
class MShopKeeperInventoryItem extends Model
{
    use HasFactory;

    protected $table = 'mshopkeeper_inventory_items';

    protected $fillable = [
        'mshopkeeper_id',
        'code',
        'name',
        'item_type',
        'barcode',
        'selling_price',
        'cost_price',
        'avg_unit_price',
        'color',
        'size',
        'material',
        'description',
        'is_item',
        'inactive',
        'is_visible',
        'is_featured',
        'price_hidden',
        'unit_id',
        'unit_name',
        'picture',
        'images_count',
        'parent_mshopkeeper_id',
        'parent_id',
        'category_mshopkeeper_id',
        'category_name',
        'total_on_hand',
        'total_ordered',
        'last_synced_at',
        'sync_status',
        'sync_error',
        'raw_data',
    ];

    protected $casts = [
        'item_type' => 'integer',
        'selling_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'avg_unit_price' => 'decimal:2',
        'is_item' => 'boolean',
        'inactive' => 'boolean',
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'price_hidden' => 'boolean',
        'total_on_hand' => 'integer',
        'total_ordered' => 'integer',
        'last_synced_at' => 'datetime',
        'raw_data' => 'array',
    ];

    /**
     * Relationships
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(MShopKeeperInventoryStock::class, 'inventory_item_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MShopKeeperCategory::class, 'category_mshopkeeper_id', 'mshopkeeper_id');
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

    public function scopeActive($query)
    {
        return $query->where('inactive', false);
    }

    public function scopeInactive($query)
    {
        return $query->where('inactive', true);
    }

    public function scopeParentItems($query)
    {
        return $query->where('is_item', false);
    }

    public function scopeChildItems($query)
    {
        return $query->where('is_item', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('total_on_hand', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('total_on_hand', '<=', 0);
    }

    public function scopeByItemType($query, int $itemType)
    {
        return $query->where('item_type', $itemType);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeHidden($query)
    {
        return $query->where('is_visible', false);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Accessors
     */
    public function getItemTypeTextAttribute(): string
    {
        return match($this->item_type) {
            1 => 'Hàng Hoá',
            2 => 'Combo',
            4 => 'Dịch vụ',
            default => 'Không xác định'
        };
    }

    public function getFormattedSellingPriceAttribute(): string
    {
        return number_format($this->selling_price) . ' VND';
    }

    public function getFormattedCostPriceAttribute(): string
    {
        return number_format($this->cost_price) . ' VND';
    }

    public function getStockStatusAttribute(): string
    {
        return match (true) {
            $this->total_on_hand > 100 => 'Nhiều',
            $this->total_on_hand > 10 => 'Vừa',
            $this->total_on_hand > 0 => 'Ít',
            default => 'Hết hàng'
        };
    }

    public function getStockStatusColorAttribute(): string
    {
        return match (true) {
            $this->total_on_hand > 100 => 'success',
            $this->total_on_hand > 10 => 'info',
            $this->total_on_hand > 0 => 'warning',
            default => 'danger'
        };
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->cost_price <= 0) return 0;
        return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Get all product images from ListPictureUrl
     */
    public function getGalleryImagesAttribute(): array
    {
        if (!$this->raw_data || !isset($this->raw_data['ListPictureUrl'])) {
            return [];
        }

        return $this->raw_data['ListPictureUrl'];
    }

    /**
     * Get count of gallery images
     */
    public function getGalleryImagesCountAttribute(): int
    {
        return count($this->gallery_images);
    }

    /**
     * Check if product has multiple images
     */
    public function getHasMultipleImagesAttribute(): bool
    {
        return $this->gallery_images_count > 1;
    }

    /**
     * Static methods
     */
    public static function findByMShopKeeperId(string $mshopkeeperId): ?self
    {
        return static::where('mshopkeeper_id', $mshopkeeperId)->first();
    }

    public static function getSyncStats(): array
    {
        $lastSyncRecord = static::synced()->orderBy('last_synced_at', 'desc')->first();
        
        return [
            'total' => static::count(),
            'synced' => static::synced()->count(),
            'errors' => static::syncErrors()->count(),
            'pending' => static::pendingSync()->count(),
            'active' => static::active()->count(),
            'inactive' => static::inactive()->count(),
            'parent_items' => static::parentItems()->count(),
            'child_items' => static::childItems()->count(),
            'in_stock' => static::inStock()->count(),
            'out_of_stock' => static::outOfStock()->count(),
            'last_sync' => $lastSyncRecord ? $lastSyncRecord->last_synced_at : null,
            'total_inventory_value' => static::active()->sum('selling_price'),
            'avg_selling_price' => static::active()->avg('selling_price'),
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
            'mshopkeeper_id' => $data['Id'] ?? null,
            'code' => $data['Code'] ?? '',
            'name' => $data['Name'] ?? '',
            'item_type' => (int) ($data['ItemType'] ?? 1),
            'barcode' => $data['Barcode'] ?? null,
            'selling_price' => (float) ($data['SellingPrice'] ?? 0),
            'cost_price' => (float) ($data['CostPrice'] ?? 0),
            'avg_unit_price' => (float) ($data['AvgUnitPrice'] ?? 0),
            'color' => $data['Color'] ?? null,
            'size' => $data['Size'] ?? null,
            'material' => $data['Material'] ?? null,
            'description' => $data['Description'] ?? null,
            'is_item' => (bool) ($data['IsItem'] ?? false),
            'inactive' => (bool) ($data['Inactive'] ?? false),
            // Không sync is_visible - để admin tự quản lý
            'unit_id' => $data['UnitId'] ?? null,
            'unit_name' => $data['UnitName'] ?? null,
            'picture' => $data['Picture'] ?? null,
            'images_count' => isset($data['ListPictureUrl']) && is_array($data['ListPictureUrl'])
                ? count($data['ListPictureUrl'])
                : (isset($data['Picture']) ? 1 : 0),
            'parent_mshopkeeper_id' => $data['ParentId'] ?? null,
            'category_mshopkeeper_id' => $data['ItemCategoryId'] ?? null,
            'category_name' => $data['ItemCategoryName'] ?? null,
        ];
    }

    /**
     * Update total stock from individual branch stocks
     */
    public function updateTotalStock(): void
    {
        $totalOnHand = $this->stocks()->sum('on_hand');
        $totalOrdered = $this->stocks()->sum('ordered');
        
        $this->update([
            'total_on_hand' => $totalOnHand,
            'total_ordered' => $totalOrdered,
        ]);
    }
}
