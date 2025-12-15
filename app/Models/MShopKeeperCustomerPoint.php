<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model cho bảng mshopkeeper_customer_points
 * Lưu trữ dữ liệu điểm thẻ thành viên khách hàng từ MShopKeeper API
 */
class MShopKeeperCustomerPoint extends Model
{
    use HasFactory;

    protected $table = 'mshopkeeper_customer_points';

    protected $fillable = [
        'original_id',
        'tel',
        'full_name',
        'total_point',
        'last_synced_at',
        'sync_status',
        'sync_error',
        'raw_data',
    ];

    protected $casts = [
        'total_point' => 'integer',
        'last_synced_at' => 'datetime',
        'raw_data' => 'array',
    ];

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

    public function scopeWithPoints($query)
    {
        return $query->where('total_point', '>', 0);
    }

    public function scopeHighValueCustomers($query, int $minPoints = 1000)
    {
        return $query->where('total_point', '>=', $minPoints);
    }

    /**
     * Accessors
     */
    public function getFormattedPointsAttribute(): string
    {
        return number_format($this->total_point) . ' điểm';
    }

    public function getPointLevelAttribute(): string
    {
        return match (true) {
            $this->total_point >= 5000 => 'VIP',
            $this->total_point >= 2000 => 'Vàng',
            $this->total_point >= 1000 => 'Bạc',
            $this->total_point >= 500 => 'Đồng',
            default => 'Thường'
        };
    }

    /**
     * Static methods
     */
    public static function findByOriginalId(string $originalId): ?self
    {
        return static::where('original_id', $originalId)->first();
    }

    public static function getSyncStats(): array
    {
        $lastSyncRecord = static::synced()->orderBy('last_synced_at', 'desc')->first();

        return [
            'total' => static::count(),
            'synced' => static::synced()->count(),
            'errors' => static::syncErrors()->count(),
            'pending' => static::pendingSync()->count(),
            'last_sync' => $lastSyncRecord ? $lastSyncRecord->last_synced_at : null,
            'total_points' => static::sum('total_point'),
            'avg_points' => static::avg('total_point'),
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
            'original_id' => $data['OriginalId'] ?? null,
            'tel' => $data['Tel'] ?? null,
            'full_name' => $data['FullName'] ?? '',
            'total_point' => (int) ($data['TotalPoint'] ?? 0),
        ];
    }

    /**
     * Relationship với customer thông thường (nếu có)
     */
    public function customer()
    {
        return $this->hasOne(MShopKeeperCustomer::class, 'tel', 'tel');
    }
}
