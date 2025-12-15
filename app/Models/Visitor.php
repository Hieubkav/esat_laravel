<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'session_id',
        'url',
        'referer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Lấy tổng số lượt truy cập
     */
    public static function getTotalVisits(): int
    {
        return static::count();
    }

    /**
     * Lấy số lượt truy cập hôm nay
     */
    public static function getTodayVisits(): int
    {
        return static::whereDate('visited_at', Carbon::today())->count();
    }

    /**
     * Lấy tổng số người dùng khác nhau (unique visitors)
     */
    public static function getTotalUniqueVisitors(): int
    {
        return static::distinct('ip_address')->count();
    }

    /**
     * Lấy số người dùng khác nhau hôm nay
     */
    public static function getTodayUniqueVisitors(): int
    {
        return static::whereDate('visited_at', Carbon::today())
            ->distinct('ip_address')
            ->count();
    }

    /**
     * Ghi lại lượt truy cập
     */
    public static function recordVisit(string $ipAddress, string $url, ?string $userAgent = null, ?string $referer = null): void
    {
        static::create([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'session_id' => session()->getId(),
            'url' => $url,
            'referer' => $referer,
            'visited_at' => now(),
        ]);
    }

    /**
     * Xóa tất cả dữ liệu tracking
     */
    public static function resetAll(): void
    {
        static::truncate();
    }
}
