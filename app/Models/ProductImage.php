<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_link',
        'alt_text',
        'is_main',
        'order',
        'status',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'status' => 'string',
        'order' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
