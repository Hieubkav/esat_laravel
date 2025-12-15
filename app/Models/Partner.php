<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class Partner extends Model
{
    use HasFactory, ClearsViewCache;

    protected $fillable = [
        'name',
        'logo_link',
        'website_link',
        'description',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
    ];
}
