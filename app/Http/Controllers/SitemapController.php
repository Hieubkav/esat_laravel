<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Post;
use App\Models\CatProduct;
use App\Models\CatPost;
use App\Models\Employee;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Generate sitemap.xml
     */
    public function index()
    {
        $sitemap = Cache::remember('sitemap_xml', 3600, function () {
            return $this->generateSitemap();
        });

        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * Generate sitemap content
     */
    private function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $xml .= $this->addUrl(route('storeFront'), now(), 'daily', '1.0');

        // Static pages
        $staticPages = [
            ['url' => route('products.categories'), 'priority' => '0.9'],
            ['url' => route('posts.categories'), 'priority' => '0.9'],
            ['url' => route('posts.services'), 'priority' => '0.8'],
            ['url' => route('posts.news'), 'priority' => '0.8'],
            ['url' => route('posts.courses'), 'priority' => '0.8'],
        ];

        foreach ($staticPages as $page) {
            $xml .= $this->addUrl($page['url'], now(), 'weekly', $page['priority']);
        }

        // Product categories
        $productCategories = CatProduct::where('status', 'active')
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($productCategories as $category) {
            $xml .= $this->addUrl(
                route('products.category', $category->slug),
                $category->updated_at,
                'weekly',
                '0.8'
            );
        }

        // Post categories
        $postCategories = CatPost::where('status', 'active')
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($postCategories as $category) {
            $xml .= $this->addUrl(
                route('posts.category', $category->slug),
                $category->updated_at,
                'weekly',
                '0.7'
            );
        }

        // Products
        $products = Product::where('status', 'active')
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($products as $product) {
            $xml .= $this->addUrl(
                route('products.show', $product->slug),
                $product->updated_at,
                'weekly',
                '0.7'
            );
        }

        // Posts
        $posts = Post::where('status', 'active')
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($posts as $post) {
            $xml .= $this->addUrl(
                route('posts.show', $post->slug),
                $post->updated_at,
                'weekly',
                '0.6'
            );
        }

        // Employees
        $employees = Employee::where('status', 'active')
            ->whereNotNull('slug')
            ->select(['slug', 'updated_at'])
            ->get();

        foreach ($employees as $employee) {
            $xml .= $this->addUrl(
                route('employee.profile', $employee->slug),
                $employee->updated_at,
                'monthly',
                '0.5'
            );
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Add URL to sitemap
     */
    private function addUrl(string $url, $lastmod = null, string $changefreq = 'weekly', string $priority = '0.5'): string
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
        
        if ($lastmod) {
            $xml .= "    <lastmod>" . $lastmod->format('Y-m-d\TH:i:s\Z') . "</lastmod>\n";
        }
        
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";

        return $xml;
    }

    /**
     * Generate robots.txt
     */
    public function robots()
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /storage/\n";
        $robots .= "Disallow: /vendor/\n";
        $robots .= "\n";
        $robots .= "Sitemap: " . route('sitemap') . "\n";

        return response($robots, 200, [
            'Content-Type' => 'text/plain'
        ]);
    }
}
