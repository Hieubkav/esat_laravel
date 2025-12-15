<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\PostView;
use App\Models\ProductView;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Chỉ track các request GET và không phải admin
        if ($request->isMethod('GET') && !$request->is('admin/*')) {
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();
            $url = $request->fullUrl();
            $referer = $request->header('referer');

            // Ghi lại lượt truy cập website
            Visitor::recordVisit($ipAddress, $url, $userAgent, $referer);

            // Kiểm tra nếu đang xem bài viết
            if ($request->route() && $request->route()->getName() === 'posts.show') {
                $slug = $request->route('slug');
                if ($slug) {
                    $post = \App\Models\Post::where('slug', $slug)->first();
                    if ($post) {
                        PostView::recordView($post->id, $ipAddress);
                    }
                }
            }

            // Kiểm tra nếu đang xem sản phẩm
            if ($request->route() && $request->route()->getName() === 'products.show') {
                $slug = $request->route('slug');
                if ($slug) {
                    $product = \App\Models\Product::where('slug', $slug)->first();
                    if ($product) {
                        ProductView::recordView($product->id, $ipAddress);
                    }
                }
            }
        }

        return $next($request);
    }
}
