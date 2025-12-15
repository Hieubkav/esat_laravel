<?php

namespace App\Http\Controllers;

use App\Models\MShopKeeperInventoryItem;
use App\Models\MShopKeeperInventoryStock;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MShopKeeperInventoryController extends Controller
{
    /**
     * Hiển thị danh sách tất cả sản phẩm với bộ lọc
     * Hỗ trợ tìm kiếm qua parameter 'search'
     */
    public function index(Request $request)
    {
        // Nếu có parameter 'q' (từ form tìm kiếm cũ), redirect về 'search'
        if ($request->has('q') && !empty($request->get('q'))) {
            $redirectParams = ['search' => $request->get('q')];

            // Convert parameter 'type' sang 'itemType' nếu có
            if ($request->has('type') && !empty($request->get('type'))) {
                $typeMapping = [
                    'hang-hoa' => '1',
                    'combo' => '2',
                    'dich-vu' => '4',
                ];
                $redirectParams['itemType'] = $typeMapping[$request->get('type')] ?? '';
            }

            return redirect()->route('mshopkeeper.inventory.index', $redirectParams);
        }

        return view('storefront.mshopkeeper.inventory.index');
    }

    /**
     * Hiển thị trang giới thiệu kho hàng
     */
    public function intro()
    {
        return view('storefront.mshopkeeper.inventory.intro');
    }



    /**
     * Hiển thị chi tiết sản phẩm
     */
    public function show($code)
    {
        $product = MShopKeeperInventoryItem::where('code', $code)
            ->where('inactive', false)
            ->where('is_visible', true)
            ->where('is_item', true)
            ->with(['stocks'])
            ->firstOrFail();

        // Sản phẩm liên quan (ưu tiên cùng danh mục, sau đó cùng loại)
        $relatedProducts = collect();

        // Nếu có danh mục, lấy sản phẩm cùng danh mục trước
        if ($product->category_name) {
            $relatedProducts = MShopKeeperInventoryItem::where('category_name', $product->category_name)
                ->where('id', '!=', $product->id)
                ->where('inactive', false)
                ->where('is_visible', true)
                ->where('is_item', true)
                ->with(['stocks'])
                ->orderBy('total_on_hand', 'desc')
                ->orderBy('selling_price', 'desc')
                ->limit(12)
                ->get();
        }

        // Nếu không đủ sản phẩm cùng danh mục, bổ sung sản phẩm cùng loại
        if ($relatedProducts->count() < 8) {
            $additionalProducts = MShopKeeperInventoryItem::where('item_type', $product->item_type)
                ->where('id', '!=', $product->id)
                ->where('inactive', false)
                ->where('is_visible', true)
                ->where('is_item', true)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->with(['stocks'])
                ->orderBy('total_on_hand', 'desc')
                ->orderBy('selling_price', 'desc')
                ->limit(8 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }

        // SEO data với breadcrumb có danh mục
        $breadcrumbs = [
            ['name' => 'Trang chủ', 'url' => route('storeFront')],
            ['name' => 'Kho hàng', 'url' => route('mshopkeeper.inventory.index')],
        ];

        // Thêm danh mục vào breadcrumb nếu có
        if ($product->category_name) {
            $breadcrumbs[] = ['name' => $product->category_name, 'url' => route('mshopkeeper.inventory.index', ['category' => $product->category_name])];
        }

        $breadcrumbs[] = ['name' => $product->name, 'url' => route('mshopkeeper.inventory.show', $product->code)];

        $seoData = [
            'title' => $product->name,
            'description' => $product->description ?: "Sản phẩm {$product->name}" . ($product->category_name ? " - {$product->category_name}" : ""),
            'ogImage' => $product->picture ?: null,
            'breadcrumbs' => $breadcrumbs
        ];

        return view('storefront.mshopkeeper.inventory.show', compact('product', 'relatedProducts', 'seoData'));
    }

    /**
     * Hiển thị trang liên hệ cho sản phẩm cụ thể
     */
    public function contact($code)
    {
        $product = MShopKeeperInventoryItem::where('code', $code)
            ->where('inactive', false)
            ->where('is_visible', true)
            ->where('is_item', true)
            ->firstOrFail();

        // SEO data
        $seoData = [
            'title' => 'Liên hệ - ' . $product->name,
            'description' => 'Liên hệ để đặt hàng ' . $product->name . ' - Vũ Phúc Baking',
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('storeFront')],
                ['name' => 'Kho hàng', 'url' => route('mshopkeeper.inventory.index')],
                ['name' => $product->name, 'url' => route('mshopkeeper.inventory.show', $product->code)],
                ['name' => 'Liên hệ', 'url' => route('mshopkeeper.product.contact', $product->code)]
            ]
        ];

        return view('storefront.mshopkeeper.inventory.contact', compact('product', 'seoData'));
    }



    /**
     * API endpoint để lấy thống kê sản phẩm
     */
    public function stats()
    {
        $stats = Cache::remember('mshopkeeper_inventory_stats', 1800, function () {
            return [
                'total_products' => MShopKeeperInventoryItem::active()->count(),
                'total_in_stock' => MShopKeeperInventoryItem::active()->inStock()->count(),
                'total_out_of_stock' => MShopKeeperInventoryItem::active()->outOfStock()->count(),
                'by_type' => [
                    'hang_hoa' => MShopKeeperInventoryItem::active()->byItemType(1)->count(),
                    'combo' => MShopKeeperInventoryItem::active()->byItemType(2)->count(),
                    'dich_vu' => MShopKeeperInventoryItem::active()->byItemType(4)->count(),
                ],
                'total_inventory_value' => MShopKeeperInventoryItem::active()->sum('selling_price'),
                'avg_price' => MShopKeeperInventoryItem::active()->avg('selling_price'),
            ];
        });

        return response()->json($stats);
    }



    /**
     * Lấy danh sách sản phẩm nổi bật (có nhiều tồn kho)
     */
    public function featured()
    {
        $products = Cache::remember('mshopkeeper_featured_products', 1800, function () {
            return MShopKeeperInventoryItem::active()
                ->inStock()
                ->orderBy('total_on_hand', 'desc')
                ->orderBy('selling_price', 'desc')
                ->limit(8)
                ->get();
        });

        return view('storefront.mshopkeeper.inventory.featured', compact('products'));
    }
}
