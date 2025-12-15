<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BroadcastsModelChanges;

class Order extends Model
{
    use HasFactory, BroadcastsModelChanges;

    protected $fillable = [
        'customer_id',
        'order_number',
        'total',
        'status',
        'payment_method',
        'payment_status',
        'shipping_address',
        'shipping_name',
        'shipping_phone',
        'shipping_email',
        'note',
        // MShopKeeper Order fields (for orders created via Quick Order)
        'mshopkeeper_order_id',
        'mshopkeeper_order_no',
        'mshopkeeper_customer_id',
        // MShopKeeper Invoice fields (for orders synced from MShopKeeper)
        'mshopkeeper_invoice_id',
        'mshopkeeper_invoice_number',
        'mshopkeeper_sync_status',
        'mshopkeeper_sync_error',
        'mshopkeeper_synced_at',
        'mshopkeeper_branch_id',
        'sale_channel',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'status' => 'string',
        'payment_method' => 'string',
        'payment_status' => 'string',
        'mshopkeeper_synced_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function calculateTotal()
    {
        $this->total = $this->items->sum('subtotal');
        $this->save();
        return $this->total;
    }

    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $timestamp = now()->format('YmdHis');
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        return $prefix . $timestamp . $random;
    }

    /**
     * Relationship với MShopKeeper Invoice
     */
    public function mshopkeeperInvoice()
    {
        return $this->belongsTo(MShopKeeperInvoice::class, 'mshopkeeper_invoice_id', 'mshopkeeper_invoice_id');
    }

    /**
     * Scope: Đơn hàng đã đồng bộ với MShopKeeper
     */
    public function scopeSyncedWithMShopKeeper($query)
    {
        return $query->where('mshopkeeper_sync_status', 'synced');
    }

    /**
     * Scope: Đơn hàng chưa đồng bộ với MShopKeeper
     */
    public function scopeNotSyncedWithMShopKeeper($query)
    {
        return $query->where('mshopkeeper_sync_status', 'not_synced');
    }

    /**
     * Scope: Đơn hàng từ website (có prefix Web)
     */
    public function scopeFromWebsite($query)
    {
        return $query->where('order_number', 'like', 'Web%');
    }

    /**
     * Đánh dấu đã đồng bộ với MShopKeeper
     */
    public function markAsSyncedWithMShopKeeper($invoiceId, $invoiceNumber): void
    {
        $this->update([
            'mshopkeeper_invoice_id' => $invoiceId,
            'mshopkeeper_invoice_number' => $invoiceNumber,
            'mshopkeeper_sync_status' => 'synced',
            'mshopkeeper_sync_error' => null,
            'mshopkeeper_synced_at' => now(),
        ]);
    }

    /**
     * Đánh dấu lỗi đồng bộ với MShopKeeper
     */
    public function markAsMShopKeeperSyncError($error): void
    {
        $this->update([
            'mshopkeeper_sync_status' => 'error',
            'mshopkeeper_sync_error' => $error,
            'mshopkeeper_synced_at' => now(),
        ]);
    }

    /**
     * Kiểm tra xem đã đồng bộ với MShopKeeper chưa
     */
    public function isSyncedWithMShopKeeper(): bool
    {
        return $this->mshopkeeper_sync_status === 'synced';
    }
}
