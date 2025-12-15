<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--ping : Ping search engines after generation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sitemap.xml and optionally ping search engines';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗺️  Generating sitemap...');

        // Clear sitemap cache
        Cache::forget('sitemap_xml');
        
        // Generate new sitemap by accessing the route
        try {
            $response = Http::get(route('sitemap'));
            
            if ($response->successful()) {
                $this->info('✅ Sitemap generated successfully!');
                $this->line('📍 URL: ' . route('sitemap'));
                
                if ($this->option('ping')) {
                    $this->pingSearchEngines();
                }
            } else {
                $this->error('❌ Failed to generate sitemap');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error generating sitemap: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Ping search engines about sitemap update
     */
    private function pingSearchEngines(): void
    {
        $this->info('📡 Pinging search engines...');

        $sitemapUrl = route('sitemap');
        $searchEngines = [
            'Google' => "https://www.google.com/ping?sitemap=" . urlencode($sitemapUrl),
            'Bing' => "https://www.bing.com/ping?sitemap=" . urlencode($sitemapUrl),
        ];

        foreach ($searchEngines as $engine => $pingUrl) {
            try {
                $response = Http::timeout(10)->get($pingUrl);
                
                if ($response->successful()) {
                    $this->line("✅ {$engine}: Pinged successfully");
                } else {
                    $this->line("⚠️  {$engine}: Ping failed (HTTP {$response->status()})");
                }
            } catch (\Exception $e) {
                $this->line("❌ {$engine}: Error - " . $e->getMessage());
            }
        }

        $this->info('📡 Search engine pinging completed!');
    }
}
