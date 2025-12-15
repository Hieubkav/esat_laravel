<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Providers\ViewServiceProvider;

class ClearViewCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:clear-cache {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear view cache data (settings, storefront, navigation, or all)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type') ?? 'all';

        $validTypes = ['settings', 'storefront', 'navigation', 'all'];

        if (!in_array($type, $validTypes)) {
            $this->error("Invalid type. Valid types are: " . implode(', ', $validTypes));
            return 1;
        }

        ViewServiceProvider::refreshCache($type);

        $this->info("View cache cleared for: {$type}");

        return 0;
    }
}
