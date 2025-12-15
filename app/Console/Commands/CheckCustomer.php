<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MShopKeeperCustomer;

class CheckCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:customer {id} {--test-registration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check customer password status and test registration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        $customer = MShopKeeperCustomer::find($id);

        if (!$customer) {
            $this->error("Customer with ID {$id} not found");
            return;
        }

        $this->info("Customer: {$customer->name}");
        $this->info("Phone: {$customer->tel}");
        $this->info("Password: " . ($customer->password ? 'HAS PASSWORD' : 'NO PASSWORD'));
        $this->info("Plain Password: " . ($customer->plain_password ?: 'NONE'));
        $this->info("HasPassword(): " . ($customer->hasPassword() ? 'TRUE' : 'FALSE'));

        if ($customer->password) {
            $this->info("Password Hash: " . substr($customer->password, 0, 20) . '...');
        }

        // Test registration if requested
        if ($this->option('test-registration')) {
            $this->info("\n=== TESTING REGISTRATION ===");
            $this->testRegistration($customer);
        }
    }

    private function testRegistration($customer)
    {
        try {
            $authService = app(\App\Services\MShopKeeperCustomerAuthService::class);

            $userData = [
                'name' => $customer->name,
                'phone' => $customer->tel,
                'email' => $customer->email,
                'password' => 'test123456',
                'gender' => $customer->gender,
                'address' => $customer->address,
                'identify_number' => $customer->identify_number,
            ];

            $this->info("Testing registration with data:");
            $this->info("- Name: {$userData['name']}");
            $this->info("- Phone: {$userData['phone']}");
            $this->info("- Email: {$userData['email']}");

            $result = $authService->register($userData);

            $this->info("Registration result:");
            $this->info("- Success: " . ($result['success'] ? 'TRUE' : 'FALSE'));
            $this->info("- Message: " . ($result['message'] ?? 'N/A'));
            $this->info("- Error: " . ($result['error'] ?? 'N/A'));

        } catch (\Exception $e) {
            $this->error("Registration test failed: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
        }
    }
}
