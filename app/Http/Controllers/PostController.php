<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\CatPost;
use App\Models\PostView;
use App\Services\SeoService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Hiển thị trang filter tổng thể cho tất cả bài viết với Livewire
     */
    public function index()
    {
        return view('storefront.posts.index');
    }

    /**
     * Hiển thị danh sách bài viết theo danh mục
     */
    public function category($slug, Request $request)
    {
        $category = CatPost::where('slug', $slug)->where('status', 'active')->firstOrFail();

        // Query builder cho bài viết
        $query = Post::whereHas('categories', function($q) use ($category) {
                $q->where('cat_post_id', $category->id);
            })
            ->where('status', 'active')
            ->with(['categories', 'images' => function($query) {
                $query->where('status', 'active')->orderBy('order');
            }]);

        // Áp dụng filter theo type nếu có
        if ($request->has('type') && in_array($request->type, ['normal', 'news', 'service', 'course'])) {
            $query->where('type', $request->type);
        }

        // Sắp xếp
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'featured':
                $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $posts = $query->paginate(12);

        return view('storefront.posts.category', compact('category', 'posts'));
    }

    /**
     * Hiển thị danh sách tất cả chuyên mục
     */
    public function categories()
    {
        $categories = CatPost::where('status', 'active')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('status', 'active')->orderBy('order');
            }])
            ->withCount(['posts' => function($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('order')
            ->get();

        return view('storefront.posts.categories', compact('categories'));
    }

    /**
     * Hiển thị chi tiết bài viết
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'images' => function($query) {
                    $query->where('status', 'active')->orderBy('order');
                },
                'categories'
            ])
            ->firstOrFail();

        // Ghi lại lượt xem
        PostView::recordView($post->id, request()->ip());

        // Bài viết liên quan - lấy từ các chuyên mục mà bài viết này thuộc về
        $categoryIds = $post->categories->pluck('id');
        $relatedPosts = Post::whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('cat_post_id', $categoryIds);
            })
            ->where('id', '!=', $post->id)
            ->where('status', 'active')
            ->with(['images' => function($query) {
                $query->where('status', 'active')->orderBy('order')->limit(1);
            }])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // SEO data
        $seoData = [
            'title' => $post->seo_title ?: $post->title,
            'description' => $post->seo_description ?: $post->excerpt,
            'ogImage' => SeoService::getPostOgImage($post),
            'structuredData' => SeoService::getPostStructuredData($post),
            'breadcrumbs' => [
                ['name' => 'Trang chủ', 'url' => route('storeFront')],
                ['name' => 'Bài viết', 'url' => route('posts.index')],
                ['name' => $post->categories->first()->name ?? 'Danh mục', 'url' => $post->categories->first() ? route('posts.category', $post->categories->first()->slug) : '#'],
                ['name' => $post->title, 'url' => route('posts.show', $post->slug)]
            ]
        ];

        return view('storefront.posts.show', compact('post', 'relatedPosts', 'seoData'));
    }

    /**
     * Hiển thị danh sách bài viết theo type (deprecated - redirect to index)
     * @deprecated Sử dụng index() method thay thế
     */
    public function byType($type, Request $request)
    {
        // Redirect to the new unified posts index with type filter
        return redirect()->route('posts.index', array_merge($request->all(), ['type' => $type]));
    }
}
