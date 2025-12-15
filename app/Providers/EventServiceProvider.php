<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Employee;
use App\Models\EmployeeImage;
use App\Models\Slider;
use App\Models\Partner;
use App\Models\Association;
use App\Models\Setting;
use App\Observers\PostObserver;
use App\Observers\PostImageObserver;
use App\Observers\ProductImageObserver;
use App\Observers\ProductObserver;
use App\Observers\EmployeeObserver;
use App\Observers\EmployeeImageObserver;
use App\Observers\SliderObserver;
use App\Observers\PartnerObserver;
use App\Observers\AssociationObserver;
use App\Observers\SettingObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Đăng ký observer cho các model có file upload
        Product::observe(ProductObserver::class);
        ProductImage::observe(ProductImageObserver::class);
        Post::observe(PostObserver::class);
        PostImage::observe(PostImageObserver::class);
        Employee::observe(EmployeeObserver::class);
        EmployeeImage::observe(EmployeeImageObserver::class);
        Slider::observe(SliderObserver::class);
        Partner::observe(PartnerObserver::class);
        Association::observe(AssociationObserver::class);
        Setting::observe(SettingObserver::class);

        // Đăng ký observer cho Order
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
