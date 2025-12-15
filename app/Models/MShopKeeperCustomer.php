<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model cho bảng mshopkeeper_customers
 * Lưu trữ dữ liệu khách hàng từ MShopKeeper API vào database
 * Đã được cập nhật để hỗ trợ authentication cho website
 */
class MShopKeeperCustomer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'mshopkeeper_customers';

    protected $fillable = [
        'mshopkeeper_id',
        'code',
        'name',
        'tel',
        'normalized_tel',
        'standard_tel',
        'addr',
        'email',
        'gender',
        'description',
        'identify_number',
        'province_addr',
        'district_addr',
        'commune_addr',
        'membership_code',
        'member_level_id',
        'member_level_name',
        'password',
        'plain_password',
        'email_verified_at',
        'last_synced_at',
        'sync_status',
        'sync_error',
        'raw_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'gender' => 'integer',
        'last_synced_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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

    /**
     * Accessors
     */
    public function getGenderTextAttribute(): string
    {
        return match($this->gender) {
            0 => 'Nam',
            1 => 'Nữ',
            default => 'Không xác định'
        };
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
        return [
            'total' => static::count(),
            'synced' => static::synced()->count(),
            'errors' => static::syncErrors()->count(),
            'pending' => static::pendingSync()->count(),
            'last_sync' => static::synced()->max('last_synced_at'),
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

    public function markAsSyncError(string $error): void
    {
        $this->update([
            'sync_status' => 'error',
            'sync_error' => $error,
        ]);
    }

    /**
     * Normalize API data structure
     */
    public static function normalizeApiData(array $data): array
    {
        return [
            'mshopkeeper_id' => $data['Id'] ?? $data['CustomerID'] ?? $data['id'] ?? null,
            'code' => $data['Code'] ?? $data['CustomerCode'] ?? null,
            'name' => $data['Name'] ?? $data['CustomerName'] ?? $data['FullName'] ?? '',
            'tel' => $data['Tel'] ?? $data['Phone'] ?? null,
            'normalized_tel' => $data['NormalizedTel'] ?? null,
            'standard_tel' => $data['StandardTel'] ?? null,
            'addr' => $data['Addr'] ?? $data['Address'] ?? null,
            'email' => $data['Email'] ?? null,
            'gender' => $data['Gender'] ?? null,
            'description' => $data['Description'] ?? $data['Note'] ?? null,
            'identify_number' => $data['IdentifyNumber'] ?? null,
            'province_addr' => $data['ProvinceAddr'] ?? $data['ProvinceName'] ?? null,
            'district_addr' => $data['DistrictAddr'] ?? $data['DistrictName'] ?? null,
            'commune_addr' => $data['CommuneAddr'] ?? $data['WardName'] ?? null,
            'membership_code' => $data['MembershipCode'] ?? $data['HardCardCode'] ?? null,
            'member_level_id' => $data['MemberLevelID'] ?? $data['CardId'] ?? null,
            'member_level_name' => $data['MemberLevelName'] ?? $data['CardName'] ?? null,
        ];
    }

    /**
     * Authentication methods
     */

    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Helper methods for authentication
     */

    /**
     * Check if customer has password set
     */
    public function hasPassword(): bool
    {
        return !empty($this->password);
    }



    /**
     * Check if customer can reset password via email
     */
    public function canResetPasswordViaEmail(): bool
    {
        return !empty($this->email);
    }
}
