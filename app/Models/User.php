<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        // if ($panel->getId() === 'dashboard') {
        //     return $this->hasRole('admin') && $this->hasVerifiedEmail();
        // }

        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
        'order',
        'status',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'string',
        'role' => 'string',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Kiểm tra xem user có phải là admin không
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Kiểm tra xem user có phải là post manager không
     */
    public function isPostManager(): bool
    {
        return $this->role === 'post_manager';
    }

    /**
     * Kiểm tra xem user có quyền truy cập resource không
     */
    public function canAccessResource(string $resource): bool
    {
        // Admin có quyền truy cập tất cả
        if ($this->isAdmin()) {
            return true;
        }

        // Quản lý bài viết chỉ có quyền truy cập Post và CatPost resources
        if ($this->isPostManager()) {
            $allowedResources = [
                'PostResource',
                'PostCategoryResource',
            ];

            return in_array($resource, $allowedResources);
        }

        return false;
    }
}
