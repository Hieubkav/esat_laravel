<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image_link',
        'position',
        'description',
        'phone',
        'email',
        'qr_code',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'order' => 'integer',
    ];

    public function images()
    {
        return $this->hasMany(EmployeeImage::class);
    }

    /**
     * Tự động tạo slug từ tên nhân viên
     */
    public function generateSlug(): string
    {
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        // Kiểm tra slug đã tồn tại chưa
        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Lấy URL trang thông tin nhân viên
     */
    public function getProfileUrl(): string
    {
        return route('employee.profile', $this->slug);
    }

    /**
     * Lấy URL QR code
     */
    public function getQrCodeUrl(): string
    {
        return route('employee.qr-code', $this->slug);
    }

    /**
     * Boot method để tự động tạo slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (empty($employee->slug)) {
                $employee->slug = $employee->generateSlug();
            }
        });

        static::updating(function ($employee) {
            if ($employee->isDirty('name') && empty($employee->slug)) {
                $employee->slug = $employee->generateSlug();
            }
        });
    }
}
