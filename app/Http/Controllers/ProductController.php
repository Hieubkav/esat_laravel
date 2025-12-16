<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CatProduct;
use App\Services\SeoService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị trang danh sách tất cả danh mục với sản phẩm và bộ lọc
     */
    public function categories()
    {
        return view('storefront.products.index');
    }

    /**
     * Hiển thị sản phẩm theo danh mục
     */
    public function category($slug)
    {
        $category = CatProduct::where('slug', $slug)
            ->where('status', true)
            ->firstOrFail();

        return view('storefront.products.index', [
            'selectedCategory' => $category
        ]);
    }

    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'productImages' => function($query) {
                    $query->where('status', 'active')->orderBy('order');
                },
                'category'
            ])
            ->firstOrFail();

        // Sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->with(['productImages' => function($query) {
                $query->where('status', 'active')->orderBy('order');
            }])
            ->orderBy('is_hot', 'desc')
            ->orderBy('order')
            ->limit(8)
            ->get();

        // SEO data
        $seoData = [
            'title' => $product->seo_title ?: $product->name,
            'description' => $product->seo_description ?: $product->description,
            'ogImage' => SeoService::getProductOgImage($product),
            'structuredData' => SeoService::getProductStructuredData($product),
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('storeFront')],
                ['name' => 'Sản phẩm', 'url' => route('products.categories')],
                ['name' => $product->name, 'url' => route('products.show', $product->slug)]
            ]
        ];

        return view('storefront.products.show', compact('product', 'relatedProducts', 'seoData'));
    }
}
