<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * Model cho bảng mshopkeeper_invoices
 * Lưu trữ dữ liệu hóa đơn từ MShopKeeper API vào database
 */
class MShopKeeperInvoice extends Model
{
    use HasFactory;

    protected $table = 'mshopkeeper_invoices';

    protected $fillable = [
        'mshopkeeper_invoice_id',
        'invoice_number',
        'invoice_type',
        'invoice_date',
        'invoice_time',
        'branch_id',
        'branch_name',
        'total_amount',
        'cost_amount',
        'tax_amount',
        'total_item_amount',
        'vat_amount',
        'discount_amount',
        'cash_amount',
        'card_amount',
        'voucher_amount',
        'debit_amount',
        'actual_amount',
        'customer_name',
        'customer_tel',
        'customer_address',
        'member_level_name',
        'cashier',
        'sale_staff',
        'payment_status',
        'payment_status_text',
        'is_cod',
        'addition_bill_type',
        'sale_channel_name',
        'return_exchange_ref_no',
        'has_connected_shipping_partner',
        'delivery_code',
        'shipping_partner_name',
        'partner_status',
        'delivery_date',
        'point',
        'barcode',
        'note',
        'last_synced_at',
        'sync_status',
        'sync_error',
        'raw_data',
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'invoice_time' => 'datetime',
        'delivery_date' => 'datetime',
        'last_synced_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'cost_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_item_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'cash_amount' => 'decimal:2',
        'card_amount' => 'decimal:2',
        'voucher_amount' => 'decimal:2',
        'debit_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'point' => 'decimal:2',
        'is_cod' => 'boolean',
        'has_connected_shipping_partner' => 'boolean',
        'raw_data' => 'array',
    ];

    /**
     * Lấy chi tiết sản phẩm từ API hoặc raw_data
     */
    public function getOrderDetailsAttribute()
    {
        // Kiểm tra cache trước
        $cacheKey = "invoice_details_{$this->id}";
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return collect($cached);
        }

        // Nếu có mshopkeeper_invoice_id, gọi API để lấy chi tiết
        if ($this->mshopkeeper_invoice_id) {
            try {
                $service = app(\App\Services\MShopKeeperService::class);
                $result = $service->getInvoiceDetailByRefId($this->mshopkeeper_invoice_id);

                if ($result['success'] && isset($result['data']['InvocieDetails'])) {
                    $details = $result['data']['InvocieDetails'];

                    // Cache kết quả trong 1 giờ
                    Cache::put($cacheKey, $details, 3600);

                    return collect($details);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch invoice details from API', [
                    'invoice_id' => $this->id,
                    'mshopkeeper_invoice_id' => $this->mshopkeeper_invoice_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback: Kiểm tra raw_data (cách cũ)
        if ($this->raw_data && isset($this->raw_data['OrderDetails'])) {
            return collect($this->raw_data['OrderDetails']);
        }

        return collect([]);
    }

    /**
     * Kiểm tra xem có chi tiết sản phẩm không
     */
    public function hasOrderDetails(): bool
    {
        return $this->order_details->isNotEmpty();
    }

    /**
     * Lấy tổng số sản phẩm
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->order_details->sum('Quantity');
    }

    /**
     * Lấy số loại sản phẩm khác nhau
     */
    public function getUniqueProductsCountAttribute(): int
    {
        return $this->order_details->count();
    }

    /**
     * Scope: Chỉ lấy hóa đơn đã sync thành công
     */
    public function scopeSynced($query)
    {
        return $query->where('sync_status', 'synced');
    }

    /**
     * Scope: Chỉ lấy hóa đơn có lỗi sync
     */
    public function scopeSyncError($query)
    {
        return $query->where('sync_status', 'error');
    }

    /**
     * Scope: Lấy hóa đơn theo kênh bán hàng
     */
    public function scopeBySaleChannel($query, $channel)
    {
        return $query->where('sale_channel_name', $channel);
    }

    /**
     * Scope: Lấy hóa đơn từ website (có chứa "Web" trong invoice_number)
     */
    public function scopeFromWebsite($query)
    {
        return $query->where('invoice_number', 'like', '%Web%');
    }

    /**
     * Scope: Lấy hóa đơn theo trạng thái thanh toán
     */
    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Lấy thống kê sync
     */
    public static function getSyncStats(): array
    {
        $total = static::count();
        $synced = static::where('sync_status', 'synced')->count();
        $errors = static::where('sync_status', 'error')->count();
        $pending = static::where('sync_status', 'pending')->count();

        $lastSync = static::whereNotNull('last_synced_at')
            ->orderBy('last_synced_at', 'desc')
            ->value('last_synced_at');

        return [
            'total' => $total,
            'synced' => $synced,
            'errors' => $errors,
            'pending' => $pending,
            'last_sync' => $lastSync,
            'sync_rate' => $total > 0 ? round(($synced / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Lấy thống kê doanh thu
     */
    public static function getRevenueStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today_revenue' => static::whereDate('invoice_date', $today)
                ->where('payment_status', 3) // Đã thanh toán
                ->sum('total_amount'),
            'today_orders' => static::whereDate('invoice_date', $today)->count(),
            'month_revenue' => static::where('invoice_date', '>=', $thisMonth)
                ->where('payment_status', 3)
                ->sum('total_amount'),
            'month_orders' => static::where('invoice_date', '>=', $thisMonth)->count(),
        ];
    }

    /**
     * Đánh dấu đã sync thành công
     */
    public function markAsSynced(): void
    {
        $this->update([
            'sync_status' => 'synced',
            'sync_error' => null,
            'last_synced_at' => Carbon::now(),
        ]);
    }

    /**
     * Đánh dấu lỗi sync
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
     * Mapping trạng thái thanh toán từ số sang text
     */
    public function getPaymentStatusTextAttribute(): string
    {
        return match($this->payment_status) {
            -1000 => 'Không xác định',
            1 => 'Chưa thanh toán',
            2 => 'Ghi nợ',
            3 => 'Đã thanh toán',
            4 => 'Đã hủy',
            5 => 'Chờ giao hàng',
            6 => 'Đang giao hàng',
            7 => 'Giao hàng thất bại',
            8 => 'Giao hàng hoàn thành',
            9 => 'Đã chuyển hoàn',
            10 => 'Chờ thu COD',
            default => 'Không xác định',
        };
    }

    /**
     * Mapping màu sắc cho trạng thái thanh toán
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            1, 10 => 'warning',      // Chưa thanh toán, Chờ thu COD
            2 => 'info',             // Ghi nợ
            3, 8 => 'success',       // Đã thanh toán, Giao hàng hoàn thành
            4, 7, 9 => 'danger',     // Đã hủy, Thất bại, Chuyển hoàn
            5, 6 => 'primary',       // Chờ giao, Đang giao
            default => 'gray',
        };
    }

    /**
     * Kiểm tra xem có phải đơn hàng từ website không
     */
    public function isFromWebsite(): bool
    {
        return str_contains($this->invoice_number, 'Web') ||
               str_contains($this->sale_channel_name ?? '', 'Website');
    }

    /**
     * Format số tiền hiển thị
     */
    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', '.') . ' ₫';
    }

    /**
     * Lấy màu sắc cho kênh bán hàng
     */
    public function getSaleChannelColorAttribute(): string
    {
        return match(strtolower($this->sale_channel_name ?? '')) {
            'website' => 'success',
            'facebook' => 'info',
            'shopee' => 'warning',
            'lazada' => 'primary',
            'tiki' => 'secondary',
            default => 'gray',
        };
    }
}
