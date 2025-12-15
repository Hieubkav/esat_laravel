<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MShopKeeperCart extends Model
{
    use HasFactory;

    protected $table = 'mshopkeeper_carts';

    protected $fillable = [
        'customer_id',
    ];

    /**
     * Quan hệ với MShopKeeperCustomer
     */
    public function customer()
    {
        return $this->belongsTo(MShopKeeperCustomer::class);
    }

    /**
     * Quan hệ với MShopKeeperCartItem
     */
    public function items()
    {
        return $this->hasMany(MShopKeeperCartItem::class, 'cart_id');
    }

    /**
     * Tính tổng số lượng sản phẩm trong giỏ hàng
     */
    public function getTotalQuantityAttribute()
    {
        // Ensure items are loaded
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }

        return $this->items->sum('quantity');
    }

    /**
     * Tính tổng giá trị giỏ hàng
     */
    public function getTotalPriceAttribute()
    {
        // Ensure items are loaded with products
        if (!$this->relationLoaded('items')) {
            $this->load('items.product');
        }

        return $this->items->sum(function ($item) {
            // Use the price stored in cart item instead of product price
            return $item->subtotal ?? ($item->quantity * ($item->product->selling_price ?? 0));
        });
    }

    /**
     * Lấy hoặc tạo giỏ hàng cho customer
     */
    public static function getOrCreateForCustomer($customerId)
    {
        return static::firstOrCreate(['customer_id' => $customerId]);
    }
}
