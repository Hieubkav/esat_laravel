<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'mshopkeeper_product_id',
        'mshopkeeper_product_code',
        'mshopkeeper_product_name',
        'product_name',
        'product_code',
        'quantity',
        'price',
        'total', // Thêm total để tương thích với service
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function mshopkeeperProduct()
    {
        return $this->belongsTo(\App\Models\MShopKeeperInventoryItem::class, 'mshopkeeper_product_id');
    }

    public function calculateSubtotal()
    {
        $this->subtotal = $this->quantity * $this->price;
        $this->save();
        return $this->subtotal;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($orderItem) {
            $orderItem->subtotal = $orderItem->quantity * $orderItem->price;
        });
    }
}
