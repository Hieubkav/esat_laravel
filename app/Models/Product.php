<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BroadcastsModelChanges;
use App\Traits\ClearsViewCache;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, BroadcastsModelChanges, ClearsViewCache;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::updating(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $query = static::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            $counter++;
        }

        return $slug;
    }

    protected $fillable = [
        'name',
        'description',
        'seo_title',
        'seo_description',
        'og_image_link',
        'slug',
        'price',
        'brand',
        'is_hot',
        'order',
        'status',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_hot' => 'boolean',
        'status' => 'string',
        'order' => 'integer',
    ];

    public function productImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Quan hệ với ProductImage
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // Quan hệ với MenuItem
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    // Quan hệ với CatProduct (category chính)
    public function category()
    {
        return $this->belongsTo(CatProduct::class, 'category_id');
    }

    // Alias cho relationship category để tương thích với ProductResource
    public function productCategory()
    {
        return $this->belongsTo(CatProduct::class, 'category_id');
    }

    // Quan hệ với CartItem
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Quan hệ với OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Lấy hình ảnh đại diện của sản phẩm (hình đầu tiên trong danh sách)
     * Sử dụng eager loaded relationship để tránh N+1 query
     *
     * @return string|null
     */
    public function getThumbnailAttribute(): ?string
    {
        // Nếu đã eager load productImages, dùng collection thay vì query mới
        if ($this->relationLoaded('productImages')) {
            $firstImage = $this->productImages->sortBy('order')->first();
            return $firstImage?->image_link;
        }

        // Fallback: query nếu chưa eager load (tránh breaking change)
        $firstImage = $this->productImages()->orderBy('order', 'asc')->first();
        return $firstImage?->image_link;
    }

}
