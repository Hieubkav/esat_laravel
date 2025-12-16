<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class HomeComponent extends Model
{
    use ClearsViewCache;

    protected $fillable = [
        'type',
        'config',
        'order',
        'active',
    ];

    protected $casts = [
        'config' => 'array',
        'order' => 'integer',
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
