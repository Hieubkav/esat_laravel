<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\BroadcastsModelChanges;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, BroadcastsModelChanges;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
        'phone',
        'address',
        'order',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'string',
        'password' => 'hashed',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }

    public function mshopkeeperCarts()
    {
        return $this->hasMany(MShopKeeperCart::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
