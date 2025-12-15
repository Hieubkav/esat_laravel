<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductView extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ip_address',
        'session_id',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Quan hệ với Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Ghi lại lượt xem sản phẩm
     */
    public static function recordView(int $productId, string $ipAddress, bool $skipDuplicateCheck = false): void
    {
        // Nếu không skip duplicate check, kiểm tra xem IP này đã xem sản phẩm này trong 5 giây qua chưa
        if (!$skipDuplicateCheck) {
            $existingView = static::where('product_id', $productId)
                ->where('ip_address', $ipAddress)
                ->where('viewed_at', '>=', Carbon::now()->subSeconds(5))
                ->first();

            if ($existingView) {
                return; // Đã xem trong 5 giây qua, không ghi lại
            }
        }

        static::create([
            'product_id' => $productId,
            'ip_address' => $ipAddress,
            'session_id' => session()->getId(),
            'viewed_at' => now(),
        ]);
    }

    /**
     * Lấy top sản phẩm được xem nhiều nhất
     */
    public static function getTopProducts(int $limit = 3)
    {
        return static::selectRaw('product_id, COUNT(*) as total_views, COUNT(DISTINCT ip_address) as unique_views')
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_views', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Xóa tất cả dữ liệu tracking
     */
    public static function resetAll(): void
    {
        static::truncate();
    }
}
