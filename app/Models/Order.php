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
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'status' => 'string',
        'payment_method' => 'string',
        'payment_status' => 'string',
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
}
