<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearFeaturedProductsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-featured-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear featured products cache for storefront';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::forget('storefront_mshopkeeper_products');
        
        $this->info('Featured products cache cleared successfully!');
        
        // Hiển thị số lượng sản phẩm featured hiện tại
        $count = \App\Models\MShopKeeperInventoryItem::where('is_featured', true)->count();
        $this->line("Current featured products count: {$count}");
        
        return 0;
    }
}
