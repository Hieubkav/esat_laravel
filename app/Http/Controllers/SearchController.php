<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MShopKeeperInventoryItem;
use App\Models\Post;

class SearchController extends Controller
{
    /**
     * Tìm kiếm sản phẩm (MSKeeper) - Redirect về kho hàng
     */
    public function products(Request $request)
    {
        $query = $request->get('q', '');

        if (!empty($query)) {
            // Redirect về trang kho hàng với parameter search
            return redirect()->route('mshopkeeper.inventory.index', [
                'search' => $query
            ]);
        }

        // Nếu không có query, redirect về trang kho hàng
        return redirect()->route('mshopkeeper.inventory.index');
    }

    /**
     * Tìm kiếm bài viết
     */
    public function posts(Request $request)
    {
        $query = $request->get('q', '');
        $posts = collect([]);
        $total = 0;

        if (!empty($query)) {
            $posts = Post::where('status', 'active')
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('content', 'like', '%' . $query . '%');
                })
                ->with(['images' => function($q) {
                    $q->where('status', 'active')->orderBy('order');
                }, 'categories'])
                ->orderBy('created_at', 'desc')
                ->paginate(12);

            $total = $posts->total();
        }

        if ($request->ajax()) {
            return response()->json([
                'posts' => $posts->items(),
                'total' => $total
            ]);
        }

        return view('search.posts', [
            'query' => $query,
            'posts' => $posts,
            'total' => $total
        ]);
    }

    /**
     * Tìm kiếm tổng hợp (cả sản phẩm và bài viết)
     */
    public function all(Request $request)
    {
        $query = $request->get('q', '');
        $products = collect([]);
        $posts = collect([]);
        $totalProducts = 0;
        $totalPosts = 0;

        if (!empty($query)) {
            // Tìm sản phẩm (MSKeeper)
            $products = MShopKeeperInventoryItem::where('inactive', false)
                ->where('is_visible', true)
                ->where('is_item', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%')
                      ->orWhere('code', 'like', '%' . $query . '%')
                      ->orWhere('barcode', 'like', '%' . $query . '%')
                      ->orWhere('category_name', 'like', '%' . $query . '%');
                })
                ->with(['stocks'])
                ->orderBy('total_on_hand', 'desc')
                ->orderBy('selling_price', 'desc')
                ->take(6)
                ->get();

            $totalProducts = MShopKeeperInventoryItem::where('inactive', false)
                ->where('is_visible', true)
                ->where('is_item', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%')
                      ->orWhere('code', 'like', '%' . $query . '%')
                      ->orWhere('barcode', 'like', '%' . $query . '%')
                      ->orWhere('category_name', 'like', '%' . $query . '%');
                })
                ->count();

            // Tìm bài viết
            $posts = Post::where('status', 'active')
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('content', 'like', '%' . $query . '%');
                })
                ->with(['images' => function($q) {
                    $q->where('status', 'active')->orderBy('order');
                }, 'categories'])
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            $totalPosts = Post::where('status', 'active')
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('content', 'like', '%' . $query . '%');
                })
                ->count();
        }

        if ($request->ajax()) {
            return response()->json([
                'products' => $products,
                'posts' => $posts,
                'totalProducts' => $totalProducts,
                'totalPosts' => $totalPosts
            ]);
        }

        return view('search.all', [
            'query' => $query,
            'products' => $products,
            'posts' => $posts,
            'totalProducts' => $totalProducts,
            'totalPosts' => $totalPosts
        ]);
    }
}
