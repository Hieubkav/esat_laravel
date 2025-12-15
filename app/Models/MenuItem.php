<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class MenuItem extends Model
{
    use HasFactory, ClearsViewCache;

    protected $fillable = [
        'parent_id',
        'label',
        'type',
        'link',
        'cat_post_id',
        'post_id',
        'cat_product_id',
        'product_id',
        'order',
        'status',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
        'order' => 'integer',
    ];

    // Quan hệ parent-child
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    // Quan hệ với các model khác
    public function catPost()
    {
        return $this->belongsTo(CatPost::class, 'cat_post_id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function catProduct()
    {
        return $this->belongsTo(CatProduct::class, 'cat_product_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Helper method để lấy URL
    public function getUrl()
    {
        switch ($this->type) {
            case 'link':
                return $this->link;
            case 'cat_post':
                if (!$this->catPost) {
                    return '#';
                }

                if ($this->catPost->type) {
                    return route('posts.index', ['type' => $this->catPost->type]);
                }

                return route('posts.index', ['category' => $this->catPost->id]);

            case 'all_posts':
                return route('posts.index');
            case 'post':
                return $this->post ? route('posts.show', $this->post->slug) : '#';
            case 'cat_product':
                return $this->catProduct ? route('products.categories', ['category' => $this->catProduct->id]) : '#';
            case 'all_products':
                return route('products.categories');
            case 'product':
                return $this->product ? route('products.show', $this->product->slug) : '#';
            case 'display_only':
                return 'javascript:void(0)';
            default:
                return '#';
        }
    }
}
