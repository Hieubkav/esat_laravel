<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class Post extends Model
{
    use HasFactory, ClearsViewCache;

    protected $fillable = [
        'title',
        'content',
        'content_builder',
        'seo_title',
        'seo_description',
        'og_image_link',
        'slug',
        'thumbnail',
        'display_thumbnail',
        'is_featured',
        'order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'content_builder' => 'array',
        'is_featured' => 'boolean',
        'display_thumbnail' => 'boolean',
        'status' => 'string',
        'order' => 'integer',
    ];



    // Quan hệ với PostImage
    public function images()
    {
        return $this->hasMany(PostImage::class);
    }

    // Quan hệ với MenuItem
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    // Quan hệ với CatPost (many-to-many)
    public function categories()
    {
        return $this->belongsToMany(CatPost::class, 'post_categories', 'post_id', 'cat_post_id');
    }

    // Alias để tương thích với code cũ - lấy category đầu tiên
    public function getCategoryAttribute()
    {
        return $this->categories()->first();
    }

    // Quan hệ với User - người tạo bài viết
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Quan hệ với User - người chỉnh sửa bài viết lần cuối
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Lấy tên người tạo bài viết
    public function getCreatorNameAttribute()
    {
        return $this->creator ? $this->creator->name : 'Không xác định';
    }

    // Lấy tên người chỉnh sửa bài viết lần cuối
    public function getUpdaterNameAttribute()
    {
        return $this->updater ? $this->updater->name : 'Không xác định';
    }
}
