<?php

namespace App\Services;

use App\Models\MShopKeeperCustomer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MShopKeeperCustomerAuthService
{
    protected $mshopkeeperService;
    
    public function __construct(MShopKeeperService $mshopkeeperService)
    {
        $this->mshopkeeperService = $mshopkeeperService;
    }
    
    /**
     * Đăng ký khách hàng mới - xử lý 3 scenarios
     */
    public function register(array $userData): array
    {
        Log::info('MShopKeeperCustomerAuthService::register started', ['phone' => $userData['phone']]);

        try {
            // 1. Ưu tiên check local trước
            $localCustomer = MShopKeeperCustomer::where('tel', $userData['phone'])->first();

            if ($localCustomer && !empty($localCustomer->password)) {
                return [
                    'success' => false,
                    'message' => 'Số điện thoại đã có tài khoản. Vui lòng đăng nhập.'
                ];
            }

            if ($localCustomer && empty($localCustomer->password)) {
                return $this->updatePasswordForExisting($localCustomer, $userData['password']);
            }

            // 2. Check trong MShopKeeper API
            $checkResult = $this->mshopkeeperService->getCustomersByInfo($userData['phone']);

            if (($checkResult['success'] ?? false) && !empty($checkResult['data']['customers'])) {
                // Tạo local record từ MShopKeeper data nếu chưa có
                $localCustomer = MShopKeeperCustomer::where('tel', $userData['phone'])->first();
                if (!$localCustomer) {
                    $localCustomer = $this->createLocalCustomer($checkResult['data']['customers'][0], null);
                }
                return $this->updatePasswordForExisting($localCustomer, $userData['password']);
            }

            // 3. Không có ở đâu → tạo mới full local + (tùy logic) tạo lên MShopkeeper nếu cần
            return $this->createNewMShopKeeperCustomer($userData);

        } catch (\Exception $e) {
            Log::error('MShopKeeper register failed', [
                'error' => $e->getMessage(),
                'user_data' => $userData,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau. Chi tiết: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Xử lý khách hàng đã tồn tại trong MShopKeeper
     */
    private function handleExistingMShopKeeperCustomer($mshopkeeperCustomer, $userData)
    {
        try {
            Log::info('Processing existing MShopKeeper customer', [
                'customer_keys' => array_keys($mshopkeeperCustomer),
                'has_customer_id' => isset($mshopkeeperCustomer['CustomerID']),
                'has_id' => isset($mshopkeeperCustomer['Id'])
            ]);

            // Normalize dữ liệu để xử lý khác biệt giữa API tạo khách hàng (Id) và API tìm kiếm (CustomerID)
            $normalizedData = MShopKeeperCustomer::normalizeApiData($mshopkeeperCustomer);
            $mshopkeeperId = $normalizedData['mshopkeeper_id'];

            if (!$mshopkeeperId) {
                Log::error('MShopKeeper customer missing Id field after normalization', [
                    'customer_data' => $mshopkeeperCustomer,
                    'normalized_data' => $normalizedData
                ]);
                throw new \Exception('Dữ liệu khách hàng từ MShopKeeper không hợp lệ (thiếu ID)');
            }

            $localCustomer = MShopKeeperCustomer::where('mshopkeeper_id', $mshopkeeperId)->first();

            if ($localCustomer && $localCustomer->hasPassword()) {
                // Scenario 3: Đã có password
                Log::info('Customer exists with password', ['customer_id' => $localCustomer->id]);
                return [
                    'success' => false,
                    'error' => 'CUSTOMER_EXISTS_WITH_PASSWORD',
                    'message' => 'Số điện thoại đã được đăng ký. Vui lòng đăng nhập.',
                    'existing_customer' => $localCustomer
                ];
            } else {
                // Scenario 2: Chưa có password, cần tạo password
                Log::info('Customer exists but no password', [
                    'local_customer_id' => $localCustomer ? $localCustomer->id : null,
                    'mshopkeeper_id' => $mshopkeeperId
                ]);

                // Đảm bảo customer tồn tại trong local database
                if (!$localCustomer) {
                    Log::info('Creating local customer record from MShopKeeper data');
                    $localCustomer = $this->createLocalCustomer($mshopkeeperCustomer, null);
                }

                return [
                    'success' => false,
                    'error' => 'CUSTOMER_EXISTS_NO_PASSWORD',
                    'message' => 'Tài khoản đã tồn tại. Vui lòng tạo mật khẩu.',
                    'existing_customer' => $mshopkeeperCustomer,
                    'local_customer' => $localCustomer,
                    'needs_password_creation' => true
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error in handleExistingMShopKeeperCustomer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'customer_data' => $mshopkeeperCustomer
            ]);
            throw $e;
        }
    }
    
    /**
     * Tạo khách hàng mới hoàn toàn
     */
    private function createNewMShopKeeperCustomer($userData)
    {
        Log::info('Creating new MShopKeeper customer');

        // 1. Tạo khách hàng trong MShopKeeper
        $customerPayload = $this->buildCustomerPayload($userData);
        Log::info('Built customer payload', ['payload' => $customerPayload]);

        $createResult = $this->mshopkeeperService->createCustomer($customerPayload);
        Log::info('MShopKeeper createCustomer result', ['success' => $createResult['success']]);

        if (!$createResult['success']) {
            if ($createResult['error']['type'] === 'CUSTOMER_EXISTS') {
                // Trường hợp race condition - khách vừa được tạo
                Log::info('Race condition detected, customer exists');
                return $this->handleExistingMShopKeeperCustomer(
                    $createResult['error']['existing_customer'],
                    $userData
                );
            }

            Log::error('Failed to create customer in MShopKeeper', ['error' => $createResult['error']]);
            throw new \Exception($createResult['error']['message']);
        }

        // 2. Lưu vào database local với password
        Log::info('Creating local customer record');
        $localCustomer = $this->createLocalCustomer($createResult['data'], $userData['password']);

        // 3. Đăng nhập tự động
        Log::info('Auto login customer');
        Auth::guard('mshopkeeper_customer')->login($localCustomer);

        // 4. Tạo admin user tương ứng
        $this->createCorrespondingAdminUser($localCustomer, $userData['password']);

        return [
            'success' => true,
            'customer' => $localCustomer,
            'message' => 'Đăng ký thành công!'
        ];
    }
    
    /**
     * Tạo mật khẩu cho khách hàng đã tồn tại
     */
    public function createPasswordForExisting(array $mshopkeeperCustomer, string $password): array
    {
        $localCustomer = null;

        try {
            $mshopkeeperId = $mshopkeeperCustomer['Id'] ?? $mshopkeeperCustomer['id'] ?? null;

            if (!$mshopkeeperId) {
                Log::error('MShopKeeper customer missing Id field', [
                    'customer_data' => $mshopkeeperCustomer
                ]);
                throw new \Exception('Dữ liệu khách hàng từ MShopKeeper không hợp lệ (thiếu ID)');
            }

            DB::transaction(function () use (&$localCustomer, $mshopkeeperCustomer, $mshopkeeperId, $password) {
                $localCustomer = MShopKeeperCustomer::where('mshopkeeper_id', $mshopkeeperId)->first();

                if (!$localCustomer) {
                    // Tạo bản ghi local từ dữ liệu MShopkeeper
                    $localCustomer = $this->createLocalCustomer($mshopkeeperCustomer, $password);
                } else {
                    $localCustomer->update([
                        'password'       => Hash::make($password),
                        'plain_password' => $password,
                    ]);
                    $localCustomer->refresh();
                }
            });

            if ($localCustomer) {
                Auth::guard('mshopkeeper_customer')->login($localCustomer, true);

                // Tạo admin user tương ứng
                $this->createCorrespondingAdminUser($localCustomer, $password);
            }

            return [
                'success'  => true,
                'customer' => $localCustomer,
                'message'  => 'Tạo mật khẩu thành công!',
            ];

        } catch (\Exception $e) {
            Log::error('Create password for existing customer failed', [
                'error' => $e->getMessage(),
                'mshopkeeper_customer' => $mshopkeeperCustomer,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ];
        }
    }
    


    /**
     * Đăng nhập khách hàng
     */
    public function login($phone, $password)
    {
        try {
            // Tìm khách hàng trong database local
            $customer = MShopKeeperCustomer::where('tel', $phone)->first();

            if (!$customer) {
                return [
                    'success' => false,
                    'error' => 'CUSTOMER_NOT_FOUND',
                    'message' => 'Số điện thoại chưa được đăng ký.'
                ];
            }

            if (!$customer->hasPassword()) {
                return [
                    'success' => false,
                    'error' => 'NO_PASSWORD',
                    'message' => 'Tài khoản chưa có mật khẩu. Vui lòng tạo mật khẩu.'
                ];
            }

            // Kiểm tra password
            if (!Hash::check($password, $customer->password)) {
                return [
                    'success' => false,
                    'error' => 'INVALID_PASSWORD',
                    'message' => 'Mật khẩu không chính xác.'
                ];
            }
            
            // Đăng nhập thành công
            Auth::guard('mshopkeeper_customer')->login($customer);
            
            return [
                'success' => true,
                'customer' => $customer,
                'message' => 'Đăng nhập thành công!'
            ];
            
        } catch (\Exception $e) {
            Log::error('MShopKeeper login failed', [
                'error' => $e->getMessage(),
                'phone' => $phone
            ]);
            
            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ];
        }
    }
    
    /**
     * Đăng xuất
     */
    public function logout()
    {
        Auth::guard('mshopkeeper_customer')->logout();
    }
    
    /**
     * Kiểm tra đăng nhập
     */
    public function check()
    {
        return Auth::guard('mshopkeeper_customer')->check();
    }
    
    /**
     * Lấy thông tin khách hàng hiện tại
     */
    public function user()
    {
        return Auth::guard('mshopkeeper_customer')->user();
    }
    
    /**
     * Build payload cho API tạo khách hàng MShopKeeper
     */
    private function buildCustomerPayload($userData)
    {
        return [
            'Name' => $userData['name'],
            'Tel' => $userData['phone'],
            'Email' => $userData['email'] ?? null,
            'Gender' => $userData['gender'] ?? 0,
            'Addr' => $userData['address'] ?? null,
            'IdentifyNumber' => $userData['identify_number'] ?? null,
            'Description' => 'Khách hàng đăng ký từ website',
            // Địa chỉ mặc định (có thể cải tiến sau)
            'AddressProvince' => 'VN101', // Hà Nội
            'AddressDistrict' => 'VN10113', // Cầu Giấy
            'AddressVillage' => 'VN1011303' // Dịch Vọng
        ];
    }
    
    /**
     * Tạo local customer từ MShopKeeper data
     */
    private function createLocalCustomer($mshopkeeperData, $password)
    {
        Log::info('Creating local customer', [
            'has_password' => !empty($password),
            'customer_keys' => array_keys($mshopkeeperData)
        ]);

        $normalizedData = MShopKeeperCustomer::normalizeApiData($mshopkeeperData);

        // Chỉ hash password nếu có password
        if (!empty($password)) {
            $normalizedData['password'] = Hash::make($password);
            $normalizedData['plain_password'] = $password;
        } else {
            $normalizedData['password'] = null;
            $normalizedData['plain_password'] = null;
        }

        $normalizedData['sync_status'] = 'synced';
        $normalizedData['last_synced_at'] = now();

        $customer = MShopKeeperCustomer::create($normalizedData);

        Log::info('Local customer created successfully', [
            'customer_id' => $customer->id,
            'mshopkeeper_id' => $customer->mshopkeeper_id,
            'has_password' => $customer->hasPassword()
        ]);

        return $customer;
    }

    /**
     * Check customer status by phone number for realtime detection
     * Returns 4 fixed statuses: HAS_PASSWORD, NEEDS_PASSWORD, NOT_FOUND, ERROR
     */
    public function checkCustomerStatus(string $phone): array
    {
        try {
            // 1. Check trong local database trước
            $localCustomer = MShopKeeperCustomer::where('tel', $phone)->first();

            if ($localCustomer && !empty($localCustomer->password)) {
                return [
                    'status' => 'HAS_PASSWORD',
                    'message' => 'Tài khoản đã tồn tại. Bạn có thể đăng nhập.'
                ];
            }

            if ($localCustomer && empty($localCustomer->password)) {
                // Local record đã có nhưng chưa đặt password → chỉ cần đặt password
                return [
                    'status' => 'NEEDS_PASSWORD',
                    'customer' => $localCustomer->only(['name', 'email', 'tel', 'gender', 'addr', 'identify_number'])
                ];
            }

            // 2. Check trong MShopKeeper API
            $checkResult = $this->mshopkeeperService->getCustomersByInfo($phone);

            if (!($checkResult['success'] ?? false)) {
                return ['status' => 'ERROR'];
            }

            $customers = $checkResult['data']['customers'] ?? [];
            if (empty($customers)) {
                return ['status' => 'NOT_FOUND'];
            }

            // Chuẩn hóa data của KH từ MShopkeeper để FE autofill
            $customer = $this->normalizeMshopkeeperData($customers[0]);
            return [
                'status' => 'NEEDS_PASSWORD',
                'customer' => $customer
            ];

        } catch (\Exception $e) {
            Log::error('Check customer status failed', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return ['status' => 'ERROR'];
        }
    }

    /**
     * Normalize MShopkeeper customer data for frontend autofill
     */
    private function normalizeMshopkeeperData(array $raw): array
    {
        return [
            'mshopkeeper_id' => $raw['Id'] ?? $raw['id'] ?? null,
            'name' => trim(($raw['Name'] ?? $raw['name'] ?? '')),
            'email' => $raw['Email'] ?? $raw['email'] ?? null,
            'tel' => $raw['Tel'] ?? $raw['tel'] ?? $raw['phone'] ?? null,
            'gender' => $raw['Gender'] ?? $raw['gender'] ?? 0,
            'addr' => $raw['Address'] ?? $raw['address'] ?? $raw['addr'] ?? null,
            'identify_number' => $raw['IdentifyNumber'] ?? $raw['identify_number'] ?? null,
        ];
    }

    /**
     * Verify customer identity before password creation
     */
    public function verifyCustomerIdentity($phone, $verificationData)
    {
        try {
            // Get customer data từ MShopKeeper hoặc local
            $customerData = null;
            $localCustomer = MShopKeeperCustomer::where('tel', $phone)->first();

            if ($localCustomer) {
                $customerData = $localCustomer->toArray();
            } else {
                // Get từ MShopKeeper API
                $checkResult = $this->mshopkeeperService->getCustomersByInfo($phone);
                if ($checkResult['success'] && !empty($checkResult['data']['customers'])) {
                    $customerData = $checkResult['data']['customers'][0];
                }
            }

            if (!$customerData) {
                return [
                    'success' => false,
                    'error' => 'CUSTOMER_NOT_FOUND',
                    'message' => 'Không tìm thấy thông tin khách hàng.'
                ];
            }

            // Verify thông tin cá nhân
            $verificationScore = 0;
            $totalChecks = 0;

            // Check tên (case insensitive, remove extra spaces)
            if (!empty($verificationData['name']) && !empty($customerData['Name'] ?? $customerData['name'])) {
                $totalChecks++;
                $customerName = strtolower(trim($customerData['Name'] ?? $customerData['name']));
                $inputName = strtolower(trim($verificationData['name']));

                if ($customerName === $inputName) {
                    $verificationScore++;
                }
            }

            // Check email nếu có
            if (!empty($verificationData['email']) && !empty($customerData['Email'] ?? $customerData['email'])) {
                $totalChecks++;
                $customerEmail = strtolower(trim($customerData['Email'] ?? $customerData['email']));
                $inputEmail = strtolower(trim($verificationData['email']));

                if ($customerEmail === $inputEmail) {
                    $verificationScore++;
                }
            }

            // Cần ít nhất 1 thông tin khớp và tỷ lệ khớp >= 80%
            $verificationRate = $totalChecks > 0 ? ($verificationScore / $totalChecks) : 0;

            if ($verificationScore > 0 && $verificationRate >= 0.8) {
                return [
                    'success' => true,
                    'verified' => true,
                    'customer_data' => $customerData,
                    'message' => 'Xác thực thành công!'
                ];
            }

            return [
                'success' => false,
                'error' => 'VERIFICATION_FAILED',
                'message' => 'Thông tin xác thực không khớp. Vui lòng kiểm tra lại.',
                'verification_score' => $verificationScore,
                'total_checks' => $totalChecks
            ];

        } catch (\Exception $e) {
            Log::error('Customer verification failed', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra khi xác thực thông tin.'
            ];
        }
    }

    /**
     * Update password cho existing customer (KISS approach)
     */
    private function updatePasswordForExisting($localCustomer, $password): array
    {
        try {
            Log::info('Updating password for existing customer', [
                'customer_id' => $localCustomer->id,
                'customer_name' => $localCustomer->name
            ]);

            // Chỉ cần update password, không cần gọi API
            $localCustomer->update([
                'password' => Hash::make($password),
                'plain_password' => $password,
            ]);

            // Auto login
            Auth::guard('mshopkeeper_customer')->login($localCustomer, true);

            // Tạo admin user tương ứng
            $this->createCorrespondingAdminUser($localCustomer, $password);

            return [
                'success' => true,
                'customer' => $localCustomer,
                'message' => 'Tạo mật khẩu thành công!'
            ];

        } catch (\Exception $e) {
            Log::error('Update password failed', [
                'customer_id' => $localCustomer->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'SYSTEM_ERROR',
                'message' => 'Có lỗi xảy ra khi tạo mật khẩu.'
            ];
        }
    }

    /**
     * Tạo admin user tương ứng với customer
     */
    private function createCorrespondingAdminUser($customer, $password)
    {
        try {
            // Kiểm tra xem đã có admin user với email này chưa
            $adminEmail = $customer->email ?: $customer->tel . '@customer.local';

            $existingUser = \App\Models\User::where('email', $adminEmail)->first();

            if ($existingUser) {
                // Cập nhật password nếu user đã tồn tại
                $existingUser->update([
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'plain_password' => $password,
                ]);

                Log::info('Updated existing admin user password', [
                    'admin_email' => $adminEmail,
                    'customer_id' => $customer->id
                ]);
            } else {
                // Tạo admin user mới
                \App\Models\User::create([
                    'name' => $customer->name ?: 'Customer ' . $customer->tel,
                    'email' => $adminEmail,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'plain_password' => $password,
                    'role' => 'post_manager', // Mặc định là post_manager, không phải admin
                    'status' => 'active',
                    'order' => 999, // Đặt order cao để không ảnh hưởng admin chính
                ]);

                Log::info('Created corresponding admin user', [
                    'admin_email' => $adminEmail,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name
                ]);
            }

        } catch (\Exception $e) {
            // Log lỗi nhưng không fail toàn bộ registration
            Log::error('Failed to create corresponding admin user', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
