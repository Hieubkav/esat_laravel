<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SeoHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:check {--fix : Auto-fix issues where possible}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check SEO health and identify issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Running SEO Health Check...');
        $this->newLine();

        $issues = [];
        $warnings = [];
        $suggestions = [];

        // Check basic settings
        $this->checkBasicSettings($issues, $warnings);

        // Check content SEO
        $this->checkContentSeo($issues, $warnings, $suggestions);

        // Check technical SEO
        $this->checkTechnicalSeo($issues, $warnings);

        // Check performance
        $this->checkPerformance($warnings, $suggestions);

        // Display results
        $this->displayResults($issues, $warnings, $suggestions);

        // Auto-fix if requested
        if ($this->option('fix')) {
            $this->autoFix($issues);
        }

        return 0;
    }

    private function checkBasicSettings(array &$issues, array &$warnings): void
    {
        $this->info('📋 Checking basic SEO settings...');

        $settings = Setting::first();

        if (!$settings) {
            $issues[] = 'No settings found - create basic site settings';
            return;
        }

        if (empty($settings->seo_title)) {
            $warnings[] = 'Missing SEO title in settings';
        }

        if (empty($settings->seo_description)) {
            $warnings[] = 'Missing SEO description in settings';
        }

        if (empty($settings->og_image_link)) {
            $warnings[] = 'Missing OG image in settings';
        }

        if (empty($settings->favicon_link)) {
            $warnings[] = 'Missing favicon in settings';
        }

        $this->line('✅ Basic settings checked');
    }

    private function checkContentSeo(array &$issues, array &$warnings, array &$suggestions): void
    {
        $this->info('📝 Checking content SEO...');

        // Check products
        $productsWithoutSeoTitle = Product::where('status', 'active')
            ->whereNull('seo_title')
            ->count();

        $productsWithoutSeoDescription = Product::where('status', 'active')
            ->whereNull('seo_description')
            ->count();

        $productsWithoutImages = Product::where('status', 'active')
            ->whereDoesntHave('images')
            ->count();

        if ($productsWithoutSeoTitle > 0) {
            $warnings[] = "{$productsWithoutSeoTitle} products missing SEO title";
        }

        if ($productsWithoutSeoDescription > 0) {
            $warnings[] = "{$productsWithoutSeoDescription} products missing SEO description";
        }

        if ($productsWithoutImages > 0) {
            $warnings[] = "{$productsWithoutImages} products missing images";
        }

        // Check posts
        $postsWithoutSeoTitle = Post::where('status', 'active')
            ->whereNull('seo_title')
            ->count();

        $postsWithoutSeoDescription = Post::where('status', 'active')
            ->whereNull('seo_description')
            ->count();

        if ($postsWithoutSeoTitle > 0) {
            $warnings[] = "{$postsWithoutSeoTitle} posts missing SEO title";
        }

        if ($postsWithoutSeoDescription > 0) {
            $warnings[] = "{$postsWithoutSeoDescription} posts missing SEO description";
        }

        $this->line('✅ Content SEO checked');
    }

    private function checkTechnicalSeo(array &$issues, array &$warnings): void
    {
        $this->info('⚙️ Checking technical SEO...');

        // Check sitemap accessibility
        try {
            $response = Http::timeout(10)->get(route('sitemap'));
            if (!$response->successful()) {
                $issues[] = 'Sitemap.xml not accessible';
            } else {
                $this->line('✅ Sitemap.xml accessible');
            }
        } catch (\Exception $e) {
            $issues[] = 'Error checking sitemap: ' . $e->getMessage();
        }

        // Check robots.txt
        try {
            $response = Http::timeout(10)->get(route('robots'));
            if (!$response->successful()) {
                $warnings[] = 'Robots.txt not accessible';
            } else {
                $this->line('✅ Robots.txt accessible');
            }
        } catch (\Exception $e) {
            $warnings[] = 'Error checking robots.txt: ' . $e->getMessage();
        }

        $this->line('✅ Technical SEO checked');
    }

    private function checkPerformance(array &$warnings, array &$suggestions): void
    {
        $this->info('🚀 Checking performance...');

        // Check cache status
        $cacheKeys = [
            'storefront_sliders',
            'storefront_categories',
            'storefront_products',
            'global_settings'
        ];

        $cachedCount = 0;
        foreach ($cacheKeys as $key) {
            if (Cache::has($key)) {
                $cachedCount++;
            }
        }

        if ($cachedCount < count($cacheKeys)) {
            $suggestions[] = 'Run "php artisan storefront:optimize --warm" to improve performance';
        } else {
            $this->line('✅ Cache warmed up');
        }

        $this->line('✅ Performance checked');
    }

    private function displayResults(array $issues, array $warnings, array $suggestions): void
    {
        $this->newLine();
        $this->info('📊 SEO Health Check Results:');
        $this->newLine();

        // Issues (Critical)
        if (!empty($issues)) {
            $this->error('🚨 Critical Issues (' . count($issues) . '):');
            foreach ($issues as $issue) {
                $this->line('  ❌ ' . $issue);
            }
            $this->newLine();
        }

        // Warnings
        if (!empty($warnings)) {
            $this->warn('⚠️  Warnings (' . count($warnings) . '):');
            foreach ($warnings as $warning) {
                $this->line('  ⚠️  ' . $warning);
            }
            $this->newLine();
        }

        // Suggestions
        if (!empty($suggestions)) {
            $this->info('💡 Suggestions (' . count($suggestions) . '):');
            foreach ($suggestions as $suggestion) {
                $this->line('  💡 ' . $suggestion);
            }
            $this->newLine();
        }

        // Overall score
        $totalChecks = 10; // Adjust based on actual checks
        $issueScore = count($issues) * 3;
        $warningScore = count($warnings) * 1;
        $score = max(0, 100 - $issueScore - $warningScore);

        $this->info("🎯 Overall SEO Score: {$score}/100");

        if ($score >= 90) {
            $this->info('🎉 Excellent SEO health!');
        } elseif ($score >= 70) {
            $this->warn('👍 Good SEO health, some improvements needed');
        } else {
            $this->error('⚠️  SEO health needs attention');
        }
    }

    private function autoFix(array $issues): void
    {
        if (empty($issues)) {
            return;
        }

        $this->info('🔧 Attempting to auto-fix issues...');

        foreach ($issues as $issue) {
            if (str_contains($issue, 'Sitemap')) {
                $this->call('sitemap:generate');
                $this->line('✅ Regenerated sitemap');
            }
        }

        $this->info('🎉 Auto-fix completed!');
    }
}
