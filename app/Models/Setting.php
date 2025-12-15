<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class Setting extends Model
{
    use HasFactory, ClearsViewCache;

    protected $fillable = [
        'site_name',
        'logo_link',
        'favicon_link',
        'seo_title',
        'seo_description',
        'og_image_link',
        'placeholder_image',
        'hotline',
        'address',
        'email',
        'slogan',
        'facebook_link',
        'zalo_link',
        'youtube_link',
        'tiktok_link',
        'messenger_link',
        'working_hours',
        'footer_description',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
    ];
}
