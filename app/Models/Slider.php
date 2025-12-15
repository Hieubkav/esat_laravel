<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsViewCache;

class Slider extends Model
{
    use HasFactory, ClearsViewCache;

    protected $fillable = [
        'image_link',
        'title',
        'description',
        'link',
        'alt_text',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
    ];

    /**
     * Boot method để tự động sinh alt_text
     */
    protected static function boot()
    {
        parent::boot();

        // Tự động sinh alt_text khi tạo mới
        static::creating(function ($slider) {
            if (empty($slider->alt_text) && !empty($slider->title)) {
                $slider->alt_text = $slider->title . ' - Vũ Phúc Baking';
            }
        });

        // Tự động sinh alt_text khi cập nhật
        static::updating(function ($slider) {
            if (empty($slider->alt_text) && !empty($slider->title)) {
                $slider->alt_text = $slider->title . ' - Vũ Phúc Baking';
            }
        });
    }

    /**
     * Accessor để đảm bảo luôn có alt_text
     */
    public function getAltTextAttribute($value)
    {
        if (empty($value) && !empty($this->title)) {
            return $this->title . ' - Vũ Phúc Baking';
        }

        return $value ?: 'Slider Banner - Vũ Phúc Baking';
    }
}
