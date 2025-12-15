<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class CatPost extends Model
{
    use HasFactory, ClearsViewCache;

    protected $table = 'cat_posts';

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
        'type',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
        'type' => 'string',
    ];

    // Quan hệ với Post (many-to-many)
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_categories', 'cat_post_id', 'post_id');
    }

    // Quan hệ parent-child
    public function parent()
    {
        return $this->belongsTo(CatPost::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CatPost::class, 'parent_id');
    }

    // Quan hệ với MenuItem
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'cat_post_id');
    }
}
