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
        'mshopkeeper_inventory_item_id',
        'mshopkeeper_category_id',
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

    // Quan hệ với các model MShopKeeper
    public function mshopkeeperInventoryItem()
    {
        return $this->belongsTo(MShopKeeperInventoryItem::class, 'mshopkeeper_inventory_item_id');
    }

    public function mshopkeeperCategory()
    {
        return $this->belongsTo(MShopKeeperCategory::class, 'mshopkeeper_category_id');
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

                // Nếu category có type (course, service, news), thêm parameter type vào URL
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

            // Các loại menu MShopKeeper
            case 'mshopkeeper_inventory':
                // Kiểm tra relationship đã được load chưa, nếu chưa thì dùng ID trực tiếp
                if ($this->relationLoaded('mshopkeeperInventoryItem') && $this->mshopkeeperInventoryItem && $this->mshopkeeperInventoryItem->code) {
                    return route('mshopkeeper.inventory.show', $this->mshopkeeperInventoryItem->code);
                } elseif ($this->mshopkeeper_inventory_item_id) {
                    // Lấy code từ database nếu relationship chưa load
                    $item = \App\Models\MShopKeeperInventoryItem::select('code')->find($this->mshopkeeper_inventory_item_id);
                    return ($item && $item->code) ? route('mshopkeeper.inventory.show', $item->code) : '#';
                }
                return '#';
            case 'all_mshopkeeper_inventory':
                return route('mshopkeeper.inventory.index');
            case 'mshopkeeper_category':
                // Kiểm tra relationship đã được load chưa, nếu chưa thì dùng ID trực tiếp
                if ($this->relationLoaded('mshopkeeperCategory') && $this->mshopkeeperCategory) {
                    return route('mshopkeeper.inventory.index', ['category' => $this->mshopkeeperCategory->name]);
                } elseif ($this->mshopkeeper_category_id) {
                    // Lấy name từ database nếu relationship chưa load
                    $category = \App\Models\MShopKeeperCategory::find($this->mshopkeeper_category_id);
                    return $category ? route('mshopkeeper.inventory.index', ['category' => $category->name]) : '#';
                }
                return '#';
            case 'all_mshopkeeper_categories':
                return route('mshopkeeper.inventory.index');

            case 'display_only':
                return 'javascript:void(0)'; // Không dẫn đến đâu cả
            default:
                return '#';
        }
    }
}
