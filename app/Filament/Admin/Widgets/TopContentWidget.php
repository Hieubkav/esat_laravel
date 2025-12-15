<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PostView;
use App\Models\ProductView;
use Filament\Widgets\Widget;

class TopContentWidget extends Widget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '5s';
    protected static string $view = 'filament.admin.widgets.top-content';

    public function getTopPosts()
    {
        return PostView::selectRaw('post_id, COUNT(*) as total_views, COUNT(DISTINCT ip_address) as unique_views')
            ->with(['post' => function($query) {
                $query->select('id', 'title', 'slug', 'thumbnail', 'created_at');
            }])
            ->groupBy('post_id')
            ->orderBy('total_views', 'desc')
            ->limit(3)
            ->get();
    }

    public function getTopProducts()
    {
        return ProductView::selectRaw('product_id, COUNT(*) as total_views, COUNT(DISTINCT ip_address) as unique_views')
            ->with(['product' => function($query) {
                $query->select('id', 'name', 'slug', 'price', 'created_at');
            }])
            ->groupBy('product_id')
            ->orderBy('total_views', 'desc')
            ->limit(3)
            ->get();
    }

    public function getPostUrl($post)
    {
        return route('posts.show', $post->slug);
    }

    public function getProductUrl($product)
    {
        return route('products.show', $product->slug);
    }
}
