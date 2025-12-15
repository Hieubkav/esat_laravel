<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MShopKeeperCartItem extends Model
{
    use HasFactory;

    protected $table = 'mshopkeeper_cart_items';

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Quan hệ với MShopKeeperCart
     */
    public function cart()
    {
        return $this->belongsTo(MShopKeeperCart::class, 'cart_id');
    }

    /**
     * Quan hệ với MShopKeeperInventoryItem (sản phẩm)
     */
    public function product()
    {
        return $this->belongsTo(MShopKeeperInventoryItem::class, 'product_id');
    }

    /**
     * Tính tổng tiền cho item này
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * ($this->product->selling_price ?? 0);
    }
}
