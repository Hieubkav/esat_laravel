<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\WebDesignService;

class WebDesignCacheMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pre-load WebDesign cache cho storefront pages
        if ($this->isStorefrontRequest($request)) {
            app(WebDesignService::class)->getAllSections();
        }

        return $next($request);
    }

    /**
     * Kiểm tra xem có phải request tới storefront không
     */
    private function isStorefrontRequest(Request $request): bool
    {
        $path = $request->path();

        // Các route cần pre-load WebDesign cache
        $storefrontRoutes = [
            '/',
            'ban-hang',
        ];

        return in_array($path, $storefrontRoutes) || $path === '/';
    }
}
