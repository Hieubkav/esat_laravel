<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BroadcastsModelChanges;
use App\Traits\ClearsViewCache;

class Product extends Model
{
    use HasFactory, BroadcastsModelChanges, ClearsViewCache;

    protected $fillable = [
        'name',
        'description',
        'seo_title',
        'seo_description',
        'og_image_link',
        'slug',
        'price',
        'compare_price',
        'brand',
        'sku',
        'stock',
        'unit',
        'is_hot',
        'order',
        'status',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock' => 'integer',
        'is_hot' => 'boolean',
        'status' => 'string',
        'order' => 'integer',
    ];

    protected $attributes = [
        'stock' => 0,
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
     *
     * @return string|null
     */
    public function getThumbnailAttribute(): ?string
    {
        // Lấy hình ảnh đầu tiên trong danh sách hình ảnh sản phẩm
        $firstImage = $this->productImages()->orderBy('order', 'asc')->first();

        return $firstImage ? $firstImage->image_link : null;
    }

    /**
     * Kiểm tra xem sản phẩm có đang giảm giá hay không
     *
     * @return bool
     */
    public function hasDiscount(): bool
    {
        return !is_null($this->compare_price) && $this->compare_price < $this->price;
    }

    /**
     * Lấy giá hiện tại của sản phẩm (giá khuyến mãi hoặc giá gốc)
     *
     * @return float
     */
    public function getCurrentPrice(): float
    {
        return $this->hasDiscount() ? $this->compare_price : $this->price;
    }

    // Quan hệ với ProductView
    public function views()
    {
        return $this->hasMany(ProductView::class);
    }

    // Lấy tổng số lượt xem
    public function getTotalViewsAttribute()
    {
        return $this->views()->count();
    }

    // Lấy số người xem khác nhau
    public function getUniqueViewsAttribute()
    {
        return $this->views()->distinct('ip_address')->count();
    }

    /**
     * Lấy phần trăm giảm giá
     *
     * @return int|null
     */
    public function getDiscountPercentage(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return (int) (100 - ($this->compare_price / $this->price * 100));
    }

    /**
     * Set stock attribute - đảm bảo luôn là số nguyên không âm
     */
    public function setStockAttribute($value)
    {
        $this->attributes['stock'] = max(0, (int) ($value ?? 0));
    }

    /**
     * Kiểm tra xem sản phẩm có còn hàng không
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Kiểm tra xem sản phẩm có sắp hết hàng không (dưới 10 sản phẩm)
     */
    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= 10;
    }

    /**
     * Lấy trạng thái tồn kho
     */
    public function getStockStatus(): string
    {
        if ($this->stock === 0) {
            return 'Hết hàng';
        } elseif ($this->stock <= 10) {
            return 'Sắp hết hàng';
        } else {
            return 'Còn hàng';
        }
    }

    /**
     * Lấy màu badge cho trạng thái tồn kho
     */
    public function getStockStatusColor(): string
    {
        if ($this->stock === 0) {
            return 'danger';
        } elseif ($this->stock <= 10) {
            return 'warning';
        } else {
            return 'success';
        }
    }
}
