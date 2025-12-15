<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class CatProduct extends Model
{
    use HasFactory, ClearsViewCache;

    protected $table = 'cat_products';

    protected $fillable = [
        'name',
        'slug',
        'seo_title',
        'seo_description',
        'og_image_link',
        'image',
        'description',
        'parent_id',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
    ];

    // Quan hệ với Product (one-to-many)
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    // Quan hệ parent-child
    public function parent()
    {
        return $this->belongsTo(CatProduct::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CatProduct::class, 'parent_id');
    }

    // Quan hệ với MenuItem
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'cat_product_id');
    }
}
