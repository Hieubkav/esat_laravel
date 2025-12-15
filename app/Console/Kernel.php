<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ========================================
        // SYNC MSHOPKEEPER DATA - 3 lần/ngày: 9h, 13h, 17h
        // ========================================

        // 1. CATEGORIES SYNC - 9:00 AM
        $schedule->command('mshopkeeper:sync-categories')
                 ->dailyAt('09:00')
                 ->withoutOverlapping(30)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper categories sync completed successfully at 9:00 AM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper categories sync failed at 9:00 AM');
                 });

        // 2. CUSTOMERS SYNC - 9:15 AM (sau categories 15 phút)
        $schedule->command('mshopkeeper:sync-customers')
                 ->dailyAt('09:15')
                 ->withoutOverlapping(30)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper customers sync completed successfully at 9:15 AM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper customers sync failed at 9:15 AM');
                 });

        // 3. INVENTORY ITEMS SYNC - 9:30 AM (sau customers 15 phút)
        $schedule->command('mshopkeeper:sync-inventory-items --include-inventory')
                 ->dailyAt('09:30')
                 ->withoutOverlapping(60)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper inventory items sync completed successfully at 9:30 AM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper inventory items sync failed at 9:30 AM');
                 });

        // 4. INVOICES SYNC - 9:45 AM (sau inventory 15 phút)
        $schedule->command('mshopkeeper:sync-invoices --from-date=' . now()->subDays(7)->format('Y-m-d') . ' --to-date=' . now()->format('Y-m-d'))
                 ->dailyAt('09:45')
                 ->withoutOverlapping(120)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper invoices sync completed successfully at 9:45 AM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper invoices sync failed at 9:45 AM');
                 });

        // ========================================
        // SYNC BUỔI CHIỀU - 13:00
        // ========================================

        // 1. CATEGORIES SYNC - 1:00 PM
        $schedule->command('mshopkeeper:sync-categories')
                 ->dailyAt('13:00')
                 ->withoutOverlapping(30)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper categories sync completed successfully at 1:00 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper categories sync failed at 1:00 PM');
                 });

        // 2. CUSTOMERS SYNC - 1:15 PM
        $schedule->command('mshopkeeper:sync-customers')
                 ->dailyAt('13:15')
                 ->withoutOverlapping(30)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper customers sync completed successfully at 1:15 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper customers sync failed at 1:15 PM');
                 });

        // 3. INVENTORY ITEMS SYNC - 1:30 PM
        $schedule->command('mshopkeeper:sync-inventory-items --include-inventory')
                 ->dailyAt('13:30')
                 ->withoutOverlapping(60)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper inventory items sync completed successfully at 1:30 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper inventory items sync failed at 1:30 PM');
                 });

        // 4. INVOICES SYNC - 1:45 PM (sau inventory 15 phút)
        $schedule->command('mshopkeeper:sync-invoices --from-date=' . now()->subDays(3)->format('Y-m-d') . ' --to-date=' . now()->format('Y-m-d'))
                 ->dailyAt('13:45')
                 ->withoutOverlapping(120)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper invoices sync completed successfully at 1:45 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper invoices sync failed at 1:45 PM');
                 });

        // ========================================
        // SYNC BUỔI TỐI - 17:00
        // ========================================

        // 1. CATEGORIES SYNC - 5:00 PM
        $schedule->command('mshopkeeper:sync-categories')
                 ->dailyAt('17:00')
                 ->withoutOverlapping(30)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper categories sync completed successfully at 5:00 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper categories sync failed at 5:00 PM');
                 });

        // 2. CUSTOMERS SYNC - 5:15 PM
        $schedule->command('mshopkeeper:sync-customers')
                 ->dailyAt('17:15')
                 ->withoutOverlapping(30)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper customers sync completed successfully at 5:15 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper customers sync failed at 5:15 PM');
                 });

        // 3. INVENTORY ITEMS SYNC - 5:30 PM
        $schedule->command('mshopkeeper:sync-inventory-items --include-inventory')
                 ->dailyAt('17:30')
                 ->withoutOverlapping(60)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper inventory items sync completed successfully at 5:30 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper inventory items sync failed at 5:30 PM');
                 });

        // 4. INVOICES SYNC - 5:45 PM (sau inventory 15 phút)
        $schedule->command('mshopkeeper:sync-invoices --from-date=' . now()->subDays(1)->format('Y-m-d') . ' --to-date=' . now()->format('Y-m-d'))
                 ->dailyAt('17:45')
                 ->withoutOverlapping(120)
                 ->runInBackground()
                 ->onSuccess(function () {
                     Log::info('MShopKeeper invoices sync completed successfully at 5:45 PM');
                 })
                 ->onFailure(function () {
                     Log::error('MShopKeeper invoices sync failed at 5:45 PM');
                 });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
