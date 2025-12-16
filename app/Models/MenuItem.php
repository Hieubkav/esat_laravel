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
        'link',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    public function getUrl()
    {
        return $this->link ?: '#';
    }
}
