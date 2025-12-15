<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CatProduct;
use App\Models\ProductView;
use App\Services\SeoService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Hiển thị danh sách sản phẩm theo danh mục
     */
    public function category($slug, Request $request)
    {
        $category = CatProduct::where('slug', $slug)->where('status', 'active')->firstOrFail();

        // Query builder cho sản phẩm
        $query = Product::where('category_id', $category->id)
            ->where('status', 'active')
            ->with(['category', 'productImages' => function($query) {
                $query->where('status', 'active')->orderBy('order');
            }]);

        // Sắp xếp
        $sortBy = $request->get('sort', 'default');
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_hot', 'desc')
                      ->orderBy('order')
                      ->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('storefront.products.category', compact('category', 'products'));
    }

    /**
     * Hiển thị trang danh sách tất cả danh mục với sản phẩm và bộ lọc
     */
    public function categories()
    {
        return view('storefront.products.index');
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

        // Ghi lại lượt xem
        ProductView::recordView($product->id, request()->ip());

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
                ['name' => $product->category->name ?? 'Danh mục', 'url' => route('products.category', $product->category->slug ?? '#')],
                ['name' => $product->name, 'url' => route('products.show', $product->slug)]
            ]
        ];

        return view('storefront.products.show', compact('product', 'relatedProducts', 'seoData'));
    }
}
