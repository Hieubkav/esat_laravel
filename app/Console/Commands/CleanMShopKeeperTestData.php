<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MShopKeeperCart;
use App\Models\MShopKeeperCartItem;
use App\Models\MShopKeeperCustomer;

class CleanMShopKeeperTestData extends Command
{
    protected $signature = 'mshopkeeper:clean-test-data 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Skip confirmation prompts}
                            {--restore-customers : Restore deleted customers from MShopKeeper API}';

    protected $description = 'Clean MShopKeeper test data (orders, carts) and optionally restore customers';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $restoreCustomers = $this->option('restore-customers');

        $this->info('🧹 MShopKeeper Test Data Cleanup');
        
        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No data will be actually deleted');
        }

        if ($restoreCustomers) {
            $this->restoreCustomers();
            return Command::SUCCESS;
        }

        // 1. Kiểm tra dữ liệu sẽ bị xóa
        $this->showDataToDelete();

        // 2. Xác nhận từ user
        if (!$force && !$dryRun) {
            if (!$this->confirm('⚠️  This will permanently delete test data. Continue?')) {
                $this->info('❌ Cleanup cancelled');
                return Command::FAILURE;
            }
        }

        // 3. Thực hiện xóa
        $this->performCleanup($dryRun);

        $this->info('✅ Cleanup completed successfully!');
        
        if (!$dryRun) {
            $this->warn('⚠️  If you accidentally deleted real customers, run:');
            $this->line('php artisan mshopkeeper:clean-test-data --restore-customers');
        }
        
        return Command::SUCCESS;
    }

    private function showDataToDelete()
    {
        $this->info('📊 Data to be deleted:');

        // Orders với mshopkeeper_order_no
        $mshopkeeperOrders = Order::whereNotNull('mshopkeeper_order_no')->count();
        $this->line("• MShopKeeper Orders: {$mshopkeeperOrders}");

        // Test orders (có prefix TEST, SIM, WEB_)
        $testOrders = Order::where(function($query) {
            $query->where('order_number', 'like', 'TEST%')
                  ->orWhere('order_number', 'like', 'SIM%')
                  ->orWhere('order_number', 'like', 'WEB_%');
        })->count();
        $this->line("• Test Orders: {$testOrders}");

        // Cart items và carts
        $cartItems = MShopKeeperCartItem::count();
        $carts = MShopKeeperCart::count();
        $this->line("• Cart Items: {$cartItems}");
        $this->line("• Carts: {$carts}");

        // KHÔNG XÓA CUSTOMERS NỮA - chỉ hiển thị thông tin
        $this->warn('⚠️  CUSTOMERS WILL NOT BE DELETED (learned from previous mistake)');
    }

    private function performCleanup($dryRun)
    {
        DB::transaction(function () use ($dryRun) {
            
            // 1. Xóa OrderItems của các đơn hàng test
            $orderItemsQuery = OrderItem::whereHas('order', function($query) {
                $query->whereNotNull('mshopkeeper_order_no')
                      ->orWhere('order_number', 'like', 'TEST%')
                      ->orWhere('order_number', 'like', 'SIM%')
                      ->orWhere('order_number', 'like', 'WEB_%');
            });

            $orderItemsCount = $orderItemsQuery->count();
            if (!$dryRun) {
                $orderItemsQuery->delete();
            }
            $this->line("🗑️  Order Items: {$orderItemsCount} " . ($dryRun ? '(would be deleted)' : 'deleted'));

            // 2. Xóa Orders test
            $ordersQuery = Order::where(function($query) {
                $query->whereNotNull('mshopkeeper_order_no')
                      ->orWhere('order_number', 'like', 'TEST%')
                      ->orWhere('order_number', 'like', 'SIM%')
                      ->orWhere('order_number', 'like', 'WEB_%');
            });

            $ordersCount = $ordersQuery->count();
            if (!$dryRun) {
                $ordersQuery->delete();
            }
            $this->line("🗑️  Orders: {$ordersCount} " . ($dryRun ? '(would be deleted)' : 'deleted'));

            // 3. Xóa Cart Items trước (foreign key constraint)
            $cartItemsCount = MShopKeeperCartItem::count();
            if (!$dryRun) {
                MShopKeeperCartItem::query()->delete();
            }
            $this->line("🗑️  Cart Items: {$cartItemsCount} " . ($dryRun ? '(would be deleted)' : 'deleted'));

            // 4. Xóa Carts sau khi đã xóa cart items
            $cartsCount = MShopKeeperCart::count();
            if (!$dryRun) {
                MShopKeeperCart::query()->delete();
            }
            $this->line("🗑️  Carts: {$cartsCount} " . ($dryRun ? '(would be deleted)' : 'deleted'));

            // KHÔNG XÓA CUSTOMERS NỮA
            $this->info("✅ Customers preserved (not deleted)");
        });
    }

    private function restoreCustomers()
    {
        $this->info('🔄 Restoring customers from MShopKeeper API...');
        
        try {
            // Chạy sync customers để khôi phục
            $this->call('mshopkeeper:sync-customers');
            
            $this->info('✅ Customer restoration completed!');
            $this->line('All customers should now have their passwords restored.');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to restore customers: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
