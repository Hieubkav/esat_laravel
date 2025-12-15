<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * Model cho bảng mshopkeeper_categories
 * Lưu trữ dữ liệu danh mục từ MShopKeeper API vào database
 */
class MShopKeeperCategory extends Model
{
    use HasFactory;

    protected $table = 'mshopkeeper_categories';

    protected $fillable = [
        'mshopkeeper_id',
        'code',
        'name',
        'description',
        'grade',
        'inactive',
        'is_leaf',
        'parent_mshopkeeper_id',
        'sort_order',
        'parent_id',
        'last_synced_at',
        'sync_status',
        'sync_error',
        'raw_data',
    ];

    protected $casts = [
        'inactive' => 'boolean',
        'is_leaf' => 'boolean',
        'grade' => 'integer',
        'sort_order' => 'integer',
        'last_synced_at' => 'datetime',
        'raw_data' => 'array',
    ];

    /**
     * Relationship: Parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relationship: Children categories
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Relationship: All descendants (recursive)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Relationship: Inventory items in this category
     */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany('App\Models\MShopKeeperInventoryItem', 'category_mshopkeeper_id', 'mshopkeeper_id');
    }

    /**
     * Get breadcrumb path for this category
     */
    public function getBreadcrumbAttribute(): string
    {
        $breadcrumbs = [];
        $current = $this;

        // Traverse up the parent chain
        while ($current) {
            array_unshift($breadcrumbs, $current->name);
            $current = $current->parent;
        }

        return implode(' > ', $breadcrumbs);
    }

    /**
     * Get breadcrumb array for this category
     */
    public function getBreadcrumbArrayAttribute(): array
    {
        $breadcrumbs = [];
        $current = $this;

        // Traverse up the parent chain
        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'mshopkeeper_id' => $current->mshopkeeper_id,
                'name' => $current->name,
                'grade' => $current->grade
            ]);
            $current = $current->parent;
        }

        return $breadcrumbs;
    }

    /**
     * Scope: Active categories only
     */
    public function scopeActive($query)
    {
        return $query->where('inactive', false);
    }

    /**
     * Scope: Inactive categories only
     */
    public function scopeInactive($query)
    {
        return $query->where('inactive', true);
    }

    /**
     * Scope: Leaf categories only
     */
    public function scopeLeaf($query)
    {
        return $query->where('is_leaf', true);
    }

    /**
     * Scope: Branch categories only
     */
    public function scopeBranch($query)
    {
        return $query->where('is_leaf', false);
    }

    /**
     * Scope: Root categories (no parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: By grade level
     */
    public function scopeByGrade($query, int $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Scope: Successfully synced
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope: Sync errors
     */
    public function scopeSyncErrors($query)
    {
        return $query->where('sync_status', 'error');
    }

    /**
     * Scope: Pending sync
     */
    public function scopePendingSync($query)
    {
        return $query->where('sync_status', 'pending');
    }

    /**
     * Accessor: Status text
     */
    public function getStatusAttribute(): string
    {
        return $this->inactive ? 'Không hoạt động' : 'Hoạt động';
    }

    /**
     * Accessor: Type text
     */
    public function getTypeAttribute(): string
    {
        return $this->is_leaf ? 'Lá' : 'Nhánh';
    }

    /**
     * Accessor: Full path name
     */
    public function getFullPathAttribute(): string
    {
        $path = collect();
        $current = $this;
        
        while ($current) {
            $path->prepend($current->name);
            $current = $current->parent;
        }
        
        return $path->implode(' > ');
    }

    /**
     * Accessor: Indented name for tree display
     */
    public function getIndentedNameAttribute(): string
    {
        $indent = str_repeat('└─ ', max(0, $this->grade));
        return $indent . $this->name;
    }

    /**
     * Accessor: Sync status badge color
     */
    public function getSyncStatusColorAttribute(): string
    {
        return match ($this->sync_status) {
            'synced' => 'success',
            'error' => 'danger',
            'pending' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Accessor: Time since last sync
     */
    public function getTimeSinceLastSyncAttribute(): ?string
    {
        if (!$this->last_synced_at) {
            return 'Chưa sync';
        }
        
        return $this->last_synced_at->diffForHumans();
    }

    /**
     * Check if category needs sync (older than threshold)
     */
    public function needsSync(int $thresholdMinutes = 30): bool
    {
        if (!$this->last_synced_at) {
            return true;
        }
        
        return $this->last_synced_at->lt(Carbon::now()->subMinutes($thresholdMinutes));
    }

    /**
     * Mark as synced successfully
     */
    public function markAsSynced(?array $rawData = null): void
    {
        $this->update([
            'sync_status' => 'synced',
            'sync_error' => null,
            'last_synced_at' => Carbon::now(),
            'raw_data' => $rawData,
        ]);
    }

    /**
     * Mark as sync error
     */
    public function markAsSyncError(string $error): void
    {
        $this->update([
            'sync_status' => 'error',
            'sync_error' => $error,
            'last_synced_at' => Carbon::now(),
        ]);
    }

    /**
     * Get tree structure starting from this category
     */
    public function getTreeStructure(): array
    {
        return [
            'id' => $this->id,
            'mshopkeeper_id' => $this->mshopkeeper_id,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'grade' => $this->grade,
            'inactive' => $this->inactive,
            'is_leaf' => $this->is_leaf,
            'sort_order' => $this->sort_order,
            'children' => $this->relationLoaded('children')
                ? $this->children->map(fn($child) => $child->getTreeStructure())->toArray()
                : [],
        ];
    }

    /**
     * Static method: Get full tree structure
     */
    public static function getFullTree(): array
    {
        // Load all categories with their relationships in one query
        $allCategories = static::with('children')->orderBy('sort_order')->get();

        // Group by parent_id for efficient lookup
        $categoriesByParent = $allCategories->groupBy('parent_id');

        // Get root categories (parent_id is null)
        $rootCategories = $categoriesByParent->get(null, collect());

        // Build tree structure recursively
        return $rootCategories->map(function ($category) use ($categoriesByParent) {
            return static::buildTreeNode($category, $categoriesByParent);
        })->toArray();
    }

    /**
     * Build tree node recursively
     */
    private static function buildTreeNode($category, $categoriesByParent): array
    {
        $children = $categoriesByParent->get($category->id, collect());

        return [
            'id' => $category->id,
            'mshopkeeper_id' => $category->mshopkeeper_id,
            'name' => $category->name,
            'code' => $category->code,
            'description' => $category->description,
            'grade' => $category->grade,
            'inactive' => $category->inactive,
            'is_leaf' => $category->is_leaf,
            'sort_order' => $category->sort_order,
            'children' => $children->map(function ($child) use ($categoriesByParent) {
                return static::buildTreeNode($child, $categoriesByParent);
            })->toArray(),
        ];
    }

    /**
     * Static method: Find by MShopKeeper ID
     */
    public static function findByMShopKeeperId(string $mshopkeeperId): ?self
    {
        return static::where('mshopkeeper_id', $mshopkeeperId)->first();
    }

    /**
     * Static method: Get sync statistics
     */
    public static function getSyncStats(): array
    {
        return [
            'total' => static::count(),
            'synced' => static::synced()->count(),
            'errors' => static::syncErrors()->count(),
            'pending' => static::pendingSync()->count(),
            'last_sync' => static::synced()->max('last_synced_at'),
        ];
    }

    /**
     * Calculate and update grade based on tree depth
     */
    public function calculateAndUpdateGrade(): int
    {
        $depth = $this->calculateDepth();

        if ($this->grade !== $depth) {
            $this->update(['grade' => $depth]);
        }

        return $depth;
    }

    /**
     * Calculate category depth in tree (0-based)
     */
    public function calculateDepth(): int
    {
        $depth = 0;
        $currentId = $this->parent_id;

        // Traverse up the tree to count depth
        while ($currentId) {
            $depth++;

            // Find parent to avoid lazy loading issues
            $parent = static::find($currentId);
            if (!$parent) {
                break;
            }

            $currentId = $parent->parent_id;

            // Prevent infinite loops
            if ($depth > 10) {
                break;
            }
        }

        return $depth;
    }

    /**
     * Calculate and update is_leaf based on children count
     */
    public function calculateAndUpdateLeafStatus(): bool
    {
        $shouldBeLeaf = $this->children()->count() === 0;

        if ($this->is_leaf !== $shouldBeLeaf) {
            $this->update(['is_leaf' => $shouldBeLeaf]);
        }

        return $shouldBeLeaf;
    }
}
