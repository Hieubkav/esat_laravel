<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class Association extends Model
{
    use HasFactory, ClearsViewCache;

    protected $fillable = [
        'name',
        'image_link',
        'description',
        'website_link',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
    ];
}
