<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\MShopKeeperCustomer;

class FixMShopKeeperCustomerPasswords extends Command
{
    protected $signature = 'mshopkeeper:fix-customer-passwords 
                            {--email= : Fix password for specific customer by email}
                            {--all : Fix passwords for all customers without password}
                            {--default-password=123456 : Default password to set}';

    protected $description = 'Fix missing passwords for MShopKeeper customers';

    public function handle()
    {
        $email = $this->option('email');
        $all = $this->option('all');
        $defaultPassword = $this->option('default-password');

        if (!$email && !$all) {
            $this->error('Please specify --email=xxx or --all');
            return Command::FAILURE;
        }

        if ($email) {
            return $this->fixSingleCustomer($email, $defaultPassword);
        }

        if ($all) {
            return $this->fixAllCustomers($defaultPassword);
        }

        return Command::SUCCESS;
    }

    private function fixSingleCustomer($email, $defaultPassword)
    {
        $this->info("🔧 Fixing password for customer: {$email}");

        $customer = MShopKeeperCustomer::where('email', $email)->first();

        if (!$customer) {
            $this->error("❌ Customer not found: {$email}");
            return Command::FAILURE;
        }

        $this->info("Found customer: {$customer->name} (ID: {$customer->id})");
        $this->info("Current password status: " . ($customer->password ? 'HAS PASSWORD' : 'NO PASSWORD'));

        if ($customer->password) {
            if (!$this->confirm('Customer already has password. Overwrite?')) {
                $this->info('❌ Cancelled');
                return Command::SUCCESS;
            }
        }

        // Set new password
        $customer->update([
            'password' => Hash::make($defaultPassword),
            'plain_password' => $defaultPassword,
        ]);

        $this->info("✅ Password set successfully!");
        $this->line("Email: {$customer->email}");
        $this->line("Password: {$defaultPassword}");
        $this->warn("⚠️  Please ask customer to change password after first login");

        return Command::SUCCESS;
    }

    private function fixAllCustomers($defaultPassword)
    {
        $this->info("🔧 Fixing passwords for all customers without password...");

        $customersWithoutPassword = MShopKeeperCustomer::whereNull('password')
            ->orWhere('password', '')
            ->get();

        $count = $customersWithoutPassword->count();

        if ($count === 0) {
            $this->info("✅ All customers already have passwords!");
            return Command::SUCCESS;
        }

        $this->info("Found {$count} customers without password");

        if (!$this->confirm("Set default password '{$defaultPassword}' for all {$count} customers?")) {
            $this->info('❌ Cancelled');
            return Command::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $fixed = 0;
        foreach ($customersWithoutPassword as $customer) {
            try {
                $customer->update([
                    'password' => Hash::make($defaultPassword),
                    'plain_password' => $defaultPassword,
                ]);
                $fixed++;
            } catch (\Exception $e) {
                $this->error("Failed to fix customer {$customer->id}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("✅ Fixed passwords for {$fixed}/{$count} customers");
        $this->line("Default password: {$defaultPassword}");
        $this->warn("⚠️  Please ask customers to change passwords after first login");

        return Command::SUCCESS;
    }
}
