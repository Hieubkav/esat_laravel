<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'total',
        'status',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'status' => 'string',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function calculateTotal()
    {
        $this->total = $this->items->sum('subtotal');
        $this->save();
        return $this->total;
    }
}
