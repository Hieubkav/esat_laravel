<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MShopKeeperService
{
    private string $appId;
    private string $domain;
    private string $secretKey;
    private string $baseUrl;
    private array $config;

    public function __construct()
    {
        try {
            $this->config = config('mshopkeeper');

            if (!$this->config) {
                throw new \Exception('MShopKeeper config not found');
            }

            $this->appId = $this->config['app_id'] ?? '';
            $this->domain = $this->config['domain'] ?? '';
            $this->secretKey = $this->config['secret_key'] ?? '';
            $this->baseUrl = $this->config['base_url'] ?? '';

            if (empty($this->appId) || empty($this->domain) || empty($this->secretKey) || empty($this->baseUrl)) {
                throw new \Exception('MShopKeeper config is incomplete');
            }
        } catch (\Throwable $e) {
            Log::error('Error initializing MShopKeeperService', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            // Set default values to prevent further errors
            $this->config = [];
            $this->appId = '';
            $this->domain = '';
            $this->secretKey = '';
            $this->baseUrl = '';
        }
    }

    /**
     * Kiểm tra xem service có được khởi tạo đúng không
     */
    public function isInitialized(): bool
    {
        return !empty($this->appId) && !empty($this->domain) && !empty($this->secretKey) && !empty($this->baseUrl);
    }

    /**
     * Tạo chữ ký HMAC theo spec MShopKeeper
     * SignatureInfo được build bằng cách mã hóa JSON params với HMACSHA256
     */
    private function generateSignatureInfo(array $loginData): string
    {
        // Sắp xếp keys theo alphabet
        ksort($loginData);

        // Tạo JSON string từ LoginData (không có spaces)
        $jsonString = json_encode($loginData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Tạo HMAC SHA256 với SecretKey
        $signature = hash_hmac('sha256', $jsonString, $this->secretKey);

        $this->logDebug('Generated SignatureInfo', [
            'login_data' => $loginData,
            'sorted_data' => $loginData,
            'json_string' => $jsonString,
            'secret_key_length' => strlen($this->secretKey),
            'signature' => $signature
        ]);

        return $signature;
    }

    /**
     * Thực hiện authentication và lấy access token
     */
    public function authenticate(): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockAuthenticate();
        }

        try {
            // Kiểm tra cache token
            $cacheKey = "mshopkeeper_token_{$this->domain}";
            $cachedToken = Cache::get($cacheKey);
            
            if ($cachedToken) {
                $this->logInfo('Using cached access token');
                return [
                    'success' => true,
                    'data' => $cachedToken,
                    'source' => 'cache'
                ];
            }

            // Tạo login time theo UTC
            $loginTime = Carbon::now('UTC')->toISOString();

            // Tạo LoginParam object (không có SignatureInfo)
            $loginParam = [
                'Domain' => $this->domain,
                'AppID' => $this->appId,
                'LoginTime' => $loginTime,
            ];

            // Tạo SignatureInfo từ LoginParam
            $signatureInfo = $this->generateSignatureInfo($loginParam);

            // Thêm SignatureInfo vào LoginParam
            $loginParam['SignatureInfo'] = $signatureInfo;

            $this->logInfo('Attempting authentication', [
                'domain' => $this->domain,
                'app_id' => $this->appId,
                'login_time' => $loginTime
            ]);

            // Gửi request
            $url = $this->baseUrl . $this->config['endpoints']['login'];
            $this->logDebug('Sending request', [
                'url' => $url,
                'login_param' => $loginParam
            ]);

            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $loginParam);

            $responseData = $response->json();

            $this->logDebug('Response received', [
                'status_code' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Authentication failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $loginResponse = $responseData['Data'];

                // Lưu token vào cache
                $tokenData = [
                    'access_token' => $loginResponse['AccessToken'],
                    'company_code' => $loginResponse['CompanyCode'],
                    'environment' => $loginResponse['Environment'],
                    'expires_at' => Carbon::now()->addSeconds($this->config['cache']['token_ttl']),
                    'domain' => $this->domain
                ];

                Cache::put($cacheKey, $tokenData, $this->config['cache']['token_ttl']);

                $this->logInfo('Authentication successful', [
                    'token_length' => strlen($loginResponse['AccessToken']),
                    'company_code' => $loginResponse['CompanyCode'],
                    'environment' => $loginResponse['Environment'],
                    'expires_at' => $tokenData['expires_at']
                ]);

                return [
                    'success' => true,
                    'data' => $tokenData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'UNKNOWN_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Authentication failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Authentication exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy danh sách categories
     */
    public function getCategories(): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetCategories();
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache categories
            $cacheKey = "mshopkeeper_categories_{$this->domain}";
            $cachedCategories = Cache::get($cacheKey);

            if ($cachedCategories) {
                $this->logInfo('Using cached categories');
                return [
                    'success' => true,
                    'data' => $cachedCategories,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching categories from API');

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['categories'];

            // Gửi request lấy categories với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->get($url, [
                    'includeInactive' => 'true' // Lấy cả categories inactive
                ]);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Categories request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $categories = $responseData['Data'];

                // Convert từ InventoryItemCategory format sang format cũ để tương thích
                $convertedCategories = $this->convertCategoriesToStandardFormat($categories);

                // Lưu vào cache
                Cache::put($cacheKey, $convertedCategories, $this->config['cache']['categories_ttl']);

                $this->logInfo('Categories fetched successfully', [
                    'count' => count($categories),
                    'total' => $responseData['Total'] ?? 0
                ]);

                return [
                    'success' => true,
                    'data' => [
                        'categories' => $convertedCategories,
                        'categories_count' => count($convertedCategories)
                    ],
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get categories failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get categories exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy danh sách categories dạng cây
     */
    public function getCategoriesTree(): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetCategoriesTree();
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache categories tree
            $cacheKey = "mshopkeeper_categories_tree_{$this->domain}";
            $cachedCategoriesTree = Cache::get($cacheKey);

            if ($cachedCategoriesTree) {
                $this->logInfo('Using cached categories tree');
                return [
                    'success' => true,
                    'data' => $cachedCategoriesTree,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching categories tree from API');

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['categories_tree'];

            // Gửi request lấy categories tree với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->get($url, [
                    'includeInactive' => 'true' // Lấy cả categories inactive
                ]);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Categories tree request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $categoriesTree = $responseData['Data'];

                // Đếm tổng số nodes trong tree
                $totalCount = $this->countTreeNodes($categoriesTree);

                $resultData = [
                    'categories_tree' => $categoriesTree,
                    'categories_count' => $totalCount,
                    'tree_depth' => $this->getTreeDepth($categoriesTree)
                ];

                // Lưu vào cache
                Cache::put($cacheKey, $resultData, $this->config['cache']['categories_ttl']);

                $this->logInfo('Categories tree fetched successfully', [
                    'total_nodes' => $totalCount,
                    'tree_depth' => $resultData['tree_depth'],
                    'root_categories' => count($categoriesTree)
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get categories tree failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get categories tree exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy danh sách chi nhánh
     */
    public function getBranchs(): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetBranchs();
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache branchs
            $cacheKey = "mshopkeeper_branchs_{$this->domain}";
            $cachedBranchs = Cache::get($cacheKey);

            if ($cachedBranchs) {
                $this->logInfo('Using cached branchs');
                return [
                    'success' => true,
                    'data' => $cachedBranchs,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching branchs from API');

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['branchs'];

            // Tạo request body
            $requestBody = [
                'IsIncludeInactiveBranch' => true,
                'IsIncludeChainOfBranch' => false
            ];

            // Gửi request lấy branchs với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestBody);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Branchs request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $branchs = $responseData['Data'];

                $resultData = [
                    'branchs' => $branchs,
                    'branchs_count' => count($branchs),
                    'base_depot_count' => count(array_filter($branchs, fn($b) => $b['IsBaseDepot'] ?? false)),
                    'chain_branch_count' => count(array_filter($branchs, fn($b) => $b['IsChainBranch'] ?? false))
                ];

                // Lưu vào cache
                Cache::put($cacheKey, $resultData, $this->config['cache']['categories_ttl']);

                $this->logInfo('Branchs fetched successfully', [
                    'total_branchs' => count($branchs),
                    'base_depots' => $resultData['base_depot_count'],
                    'chain_branchs' => $resultData['chain_branch_count']
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get branchs failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get branchs exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy danh sách khách hàng (phân trang)
     */
    public function getCustomers(int $page = 1, int $limit = 10): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetCustomers($page, $limit);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache customers
            $cacheKey = "mshopkeeper_customers_{$this->domain}_p{$page}_l{$limit}";
            $cachedCustomers = Cache::get($cacheKey);

            if ($cachedCustomers) {
                $this->logInfo('Using cached customers');
                return [
                    'success' => true,
                    'data' => $cachedCustomers,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching customers from API', ['page' => $page, 'limit' => $limit]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['customers'];

            // Tạo request body
            $requestBody = [
                'Page' => $page,
                'Limit' => min($limit, 100), // Max 100 theo spec
                'SortField' => 'Name',
                'SortType' => 1,
                'LastSyncDate' => null
            ];

            // Gửi request lấy customers với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestBody);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Customers request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $customers = $responseData['Data'];
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'customers' => $customers,
                    'customers_count' => count($customers),
                    'total_customers' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $limit > 0 ? ceil($total / $limit) : 0
                ];

                // Lưu vào cache (thời gian ngắn hơn vì dữ liệu có thể thay đổi)
                Cache::put($cacheKey, $resultData, 300); // 5 minutes

                $this->logInfo('Customers fetched successfully', [
                    'page_customers' => count($customers),
                    'total_customers' => $total,
                    'page' => $page,
                    'limit' => $limit
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get customers failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get customers exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy danh sách hạng thẻ thành viên
     */
    public function getMemberLevels(int $page = 1, int $limit = 50): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetMemberLevels($page, $limit);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache member levels
            $cacheKey = "mshopkeeper_member_levels_{$this->domain}_p{$page}_l{$limit}";
            $cachedMemberLevels = Cache::get($cacheKey);

            if ($cachedMemberLevels) {
                $this->logInfo('Using cached member levels');
                return [
                    'success' => true,
                    'data' => $cachedMemberLevels,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching member levels from API', ['page' => $page, 'limit' => $limit]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['member_levels'];

            // Tạo request body
            $requestBody = [
                'Page' => $page,
                'Limit' => min($limit, 100), // Max 100 theo spec
                'SortField' => 'MemberLevelName',
                'SortType' => 1,
                'LastSyncDate' => null
            ];

            // Gửi request lấy member levels với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestBody);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Member levels request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $memberLevels = $responseData['Data'];
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'member_levels' => $memberLevels,
                    'member_levels_count' => count($memberLevels),
                    'total_member_levels' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $limit > 0 ? ceil($total / $limit) : 0
                ];

                // Lưu vào cache
                Cache::put($cacheKey, $resultData, $this->config['cache']['categories_ttl']);

                $this->logInfo('Member levels fetched successfully', [
                    'page_member_levels' => count($memberLevels),
                    'total_member_levels' => $total,
                    'page' => $page,
                    'limit' => $limit
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get member levels failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get member levels exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Convert InventoryItemCategory format sang format chuẩn
     */
    private function convertCategoriesToStandardFormat(array $categories): array
    {
        $converted = [];

        foreach ($categories as $category) {
            // Tạo code thông minh nếu không có từ API
            $code = $category['Code'] ?? '';
            if (empty($code)) {
                // Fallback 1: Sử dụng ID nếu có
                if (!empty($category['Id'])) {
                    $code = 'CAT_' . substr($category['Id'], -8); // Lấy 8 ký tự cuối của ID
                } else {
                    // Fallback 2: Tạo từ tên (slug)
                    $code = 'CAT_' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $category['Name'] ?? 'UNKNOWN'), 0, 8));
                }
            }

            $converted[] = [
                'id' => $category['Id'],
                'name' => $category['Name'],
                'description' => $category['Description'] ?? '',
                'parent_id' => $category['ParentId'] ?? null,
                'status' => $category['Inactive'] ? 'inactive' : 'active',
                'code' => $code,
                'grade' => $category['Grade'] ?? 1,
                'is_leaf' => $category['IsLeaf'] ?? false,
                'sort_order' => $category['SortOrder'] ?? 0,
                'created_at' => now()->toISOString()
            ];
        }

        return $converted;
    }

    /**
     * Lấy thông báo lỗi theo ErrorType
     */
    private function getErrorMessage($errorType): string
    {
        return $this->config['error_types'][$errorType] ?? "Lỗi không xác định: {$errorType}";
    }

    /**
     * Logging methods
     */
    private function logInfo(string $message, array $context = []): void
    {
        if ($this->config['logging']['enabled']) {
            Log::channel($this->config['logging']['channel'])->info("[MShopKeeper] {$message}", $context);
        }
    }

    private function logError(string $message, array $context = []): void
    {
        if ($this->config['logging']['enabled']) {
            Log::channel($this->config['logging']['channel'])->error("[MShopKeeper] {$message}", $context);
        }
    }

    private function logDebug(string $message, array $context = []): void
    {
        if ($this->config['logging']['enabled'] && $this->config['logging']['level'] === 'debug') {
            Log::channel($this->config['logging']['channel'])->debug("[MShopKeeper] {$message}", $context);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(): bool
    {
        try {
            // Clear specific cache keys
            $keys = [
                "mshopkeeper_token_{$this->domain}",
                "mshopkeeper_categories_{$this->domain}",
                "mshopkeeper_categories_tree_{$this->domain}",
                "mshopkeeper_branchs_{$this->domain}",
            ];

            // Clear exact keys
            foreach ($keys as $key) {
                Cache::forget($key);
            }

            // Clear pattern-based keys (simulate pattern matching)
            $patterns = [
                "mshopkeeper_customers_{$this->domain}",
                "mshopkeeper_member_levels_{$this->domain}",
                "mshopkeeper_customers_by_info_{$this->domain}",
                "mshopkeeper_lomas_customer_{$this->domain}",
                "mshopkeeper_customers_point_paging_{$this->domain}",
            ];

            // For file-based cache, we'll clear common variations
            foreach ($patterns as $pattern) {
                // Clear base pattern
                Cache::forget($pattern);

                // Clear common variations with page/limit parameters
                for ($page = 1; $page <= 10; $page++) {
                    for ($limit = 10; $limit <= 100; $limit += 10) {
                        Cache::forget("{$pattern}_p{$page}_l{$limit}");
                    }
                }

                // Clear hash-based keys (for search terms)
                $commonSearches = ['0987555222', '0326643186', 'vanbinh@email.com', 'nguyenphuc@email.com'];
                foreach ($commonSearches as $search) {
                    Cache::forget($pattern . '_' . md5($search));
                }
            }

            $this->logInfo('Cache cleared successfully');
            return true;
        } catch (\Exception $e) {
            $this->logError('Failed to clear cache', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Mock authentication for testing
     */
    private function mockAuthenticate(): array
    {
        $this->logInfo('Using mock authentication');

        $tokenData = [
            'access_token' => 'mock_token_' . bin2hex(random_bytes(16)),
            'expires_at' => Carbon::now()->addSeconds($this->config['cache']['token_ttl']),
            'domain' => $this->domain
        ];

        return [
            'success' => true,
            'data' => $tokenData,
            'source' => 'mock'
        ];
    }

    /**
     * Mock categories for testing - 125 categories for bakery business
     */
    private function mockGetCategories(): array
    {
        $this->logInfo('Using mock categories');

        $categories = $this->generateBakeryCategories();

        return [
            'success' => true,
            'data' => [
                'categories' => $categories,
                'categories_count' => count($categories)
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Generate 125 realistic bakery categories
     */
    private function generateBakeryCategories(): array
    {
        $categories = [];
        $id = 1;

        // 1. BÁNH NGỌT (40 categories)
        $categories[] = ['id' => $id++, 'name' => 'Bánh ngọt', 'description' => 'Các loại bánh ngọt truyền thống và hiện đại', 'parent_id' => null, 'status' => 'active', 'code' => 'BANH_NGOT', 'created_at' => '2024-01-01T00:00:00Z'];

        // Bánh kem (10 items)
        $cakeParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh kem', 'description' => 'Bánh kem sinh nhật và sự kiện', 'parent_id' => 1, 'status' => 'active', 'code' => 'BANH_KEM', 'created_at' => '2024-01-01T00:00:00Z'];
        $cakeItems = ['Bánh kem sinh nhật', 'Bánh kem cưới', 'Bánh kem chocolate', 'Bánh kem vanilla', 'Bánh kem strawberry', 'Bánh kem tiramisu', 'Bánh kem red velvet', 'Bánh kem cheesecake', 'Bánh kem mousse', 'Bánh kem fondant'];
        $cakeIndex = 1;
        foreach ($cakeItems as $item) {
            $code = 'KEM_' . str_pad($cakeIndex++, 2, '0', STR_PAD_LEFT);
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "Bánh kem $item thơm ngon", 'parent_id' => $cakeParentId, 'status' => 'active', 'code' => $code, 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh quy (8 items)
        $cookieParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh quy', 'description' => 'Bánh quy giòn tan thơm ngon', 'parent_id' => 1, 'status' => 'active', 'code' => 'BANH_QUY', 'created_at' => '2024-01-01T00:00:00Z'];
        $cookieItems = ['Bánh quy bơ', 'Bánh quy chocolate chip', 'Bánh quy yến mạch', 'Bánh quy gừng', 'Bánh quy dừa', 'Bánh quy matcha', 'Bánh quy hạnh nhân', 'Bánh quy socola'];
        $cookieIndex = 1;
        foreach ($cookieItems as $item) {
            $code = 'QUY_' . str_pad($cookieIndex++, 2, '0', STR_PAD_LEFT);
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "Bánh quy $item giòn rụm", 'parent_id' => $cookieParentId, 'status' => 'active', 'code' => $code, 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh su kem (6 items)
        $profiteroleParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh su kem', 'description' => 'Bánh su kem nhân đa dạng', 'parent_id' => 1, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $profiteroleItems = ['Su kem vanilla', 'Su kem chocolate', 'Su kem dâu', 'Su kem matcha', 'Su kem caramel', 'Su kem durian'];
        foreach ($profiteroleItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "Bánh $item mềm mịn", 'parent_id' => $profiteroleParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh tart (6 items)
        $tartParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh tart', 'description' => 'Bánh tart trái cây và kem', 'parent_id' => 1, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $tartItems = ['Tart dâu', 'Tart kiwi', 'Tart chocolate', 'Tart lemon', 'Tart trái cây', 'Tart custard'];
        foreach ($tartItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "Bánh $item tươi ngon", 'parent_id' => $tartParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh muffin (5 items)
        $muffinParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh muffin', 'description' => 'Bánh muffin xốp mềm', 'parent_id' => 1, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $muffinItems = ['Muffin blueberry', 'Muffin chocolate', 'Muffin vanilla', 'Muffin chuối', 'Muffin bắp'];
        foreach ($muffinItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "Bánh $item xốp mềm", 'parent_id' => $muffinParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // 2. BÁNH MÌ (30 categories)
        $categories[] = ['id' => $id++, 'name' => 'Bánh mì', 'description' => 'Bánh mì tươi ngon hàng ngày', 'parent_id' => null, 'status' => 'active', 'code' => 'BANH_MI', 'created_at' => '2024-01-01T00:00:00Z'];

        // Bánh mì Việt Nam (10 items)
        $vietnamBreadParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh mì Việt Nam', 'description' => 'Bánh mì truyền thống Việt Nam', 'parent_id' => 2, 'status' => 'active', 'code' => 'MI_VN', 'created_at' => '2024-01-01T00:00:00Z'];
        $vietnamBreadItems = ['Bánh mì thịt nướng', 'Bánh mì pate', 'Bánh mì chả cá', 'Bánh mì xíu mại', 'Bánh mì thịt nguội', 'Bánh mì gà', 'Bánh mì chay', 'Bánh mì trứng', 'Bánh mì bò kho', 'Bánh mì heo quay'];
        $breadIndex = 1;
        foreach ($vietnamBreadItems as $item) {
            $code = 'MI_' . str_pad($breadIndex++, 2, '0', STR_PAD_LEFT);
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item truyền thống", 'parent_id' => $vietnamBreadParentId, 'status' => 'active', 'code' => $code, 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh mì sandwich (8 items)
        $sandwichParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh mì sandwich', 'description' => 'Bánh mì sandwich phong cách Âu', 'parent_id' => 2, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $sandwichItems = ['Club sandwich', 'BLT sandwich', 'Tuna sandwich', 'Chicken sandwich', 'Ham sandwich', 'Veggie sandwich', 'Grilled cheese', 'Panini'];
        foreach ($sandwichItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item phong cách Âu", 'parent_id' => $sandwichParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh mì ngọt (6 items)
        $sweetBreadParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh mì ngọt', 'description' => 'Bánh mì ngọt mềm mại', 'parent_id' => 2, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $sweetBreadItems = ['Bánh mì nho khô', 'Bánh mì chocolate', 'Bánh mì dừa', 'Bánh mì mật ong', 'Bánh mì cinnamon', 'Bánh mì brioche'];
        foreach ($sweetBreadItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item mềm ngọt", 'parent_id' => $sweetBreadParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh mì đặc biệt (5 items)
        $specialBreadParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh mì đặc biệt', 'description' => 'Bánh mì công thức đặc biệt', 'parent_id' => 2, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $specialBreadItems = ['Bánh mì sourdough', 'Bánh mì multigrain', 'Bánh mì baguette', 'Bánh mì focaccia', 'Bánh mì ciabatta'];
        foreach ($specialBreadItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item đặc biệt", 'parent_id' => $specialBreadParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // 3. ĐỒ UỐNG (35 categories)
        $categories[] = ['id' => $id++, 'name' => 'Đồ uống', 'description' => 'Cà phê, trà và các loại đồ uống khác', 'parent_id' => null, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];

        // Cà phê (12 items)
        $coffeeParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Cà phê', 'description' => 'Các loại cà phê rang xay', 'parent_id' => 3, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $coffeeItems = ['Espresso', 'Americano', 'Cappuccino', 'Latte', 'Macchiato', 'Mocha', 'Flat White', 'Affogato', 'Cold Brew', 'Iced Coffee', 'Vietnamese Coffee', 'Frappuccino'];
        foreach ($coffeeItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "Cà phê $item thơm đậm", 'parent_id' => $coffeeParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Trà (10 items)
        $teaParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Trà', 'description' => 'Trà các loại và trà sữa', 'parent_id' => 3, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $teaItems = ['Trà sữa truyền thống', 'Trà sữa matcha', 'Trà sữa chocolate', 'Trà sữa dâu', 'Trà xanh', 'Trà đen', 'Trà oolong', 'Trà gừng', 'Trà chanh', 'Bubble tea'];
        foreach ($teaItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item thơm mát", 'parent_id' => $teaParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Nước ép (6 items)
        $juiceParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Nước ép', 'description' => 'Nước ép trái cây tươi', 'parent_id' => 3, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $juiceItems = ['Nước ép cam', 'Nước ép táo', 'Nước ép dứa', 'Nước ép dưa hấu', 'Nước ép cà rốt', 'Smoothie mix'];
        foreach ($juiceItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item tươi mát", 'parent_id' => $juiceParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Đồ uống khác (5 items)
        $otherDrinkParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Đồ uống khác', 'description' => 'Các loại đồ uống đặc biệt', 'parent_id' => 3, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $otherDrinkItems = ['Soda', 'Nước khoáng', 'Chocolate nóng', 'Milkshake', 'Kombucha'];
        foreach ($otherDrinkItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item đặc biệt", 'parent_id' => $otherDrinkParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // 4. BÁNH TRUYỀN THỐNG (20 categories)
        $categories[] = ['id' => $id++, 'name' => 'Bánh truyền thống', 'description' => 'Bánh truyền thống Việt Nam', 'parent_id' => null, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];

        // Bánh tét, bánh chưng (5 items)
        $traditionalCakeParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh tết', 'description' => 'Bánh truyền thống ngày tết', 'parent_id' => 4, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $tetCakeItems = ['Bánh chưng', 'Bánh tét', 'Bánh ít', 'Bánh dày', 'Bánh phu thê'];
        foreach ($tetCakeItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item truyền thống", 'parent_id' => $traditionalCakeParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh dân gian (8 items)
        $folkCakeParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh dân gian', 'description' => 'Bánh dân gian các vùng miền', 'parent_id' => 4, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $folkCakeItems = ['Bánh flan', 'Bánh bò', 'Bánh chuối', 'Bánh khoai', 'Bánh cam', 'Bánh đậu xanh', 'Bánh dẻo', 'Bánh nướng'];
        foreach ($folkCakeItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item dân gian", 'parent_id' => $folkCakeParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Bánh miền Nam (5 items)
        $southernCakeParentId = $id;
        $categories[] = ['id' => $id++, 'name' => 'Bánh miền Nam', 'description' => 'Bánh đặc sản miền Nam', 'parent_id' => 4, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        $southernCakeItems = ['Bánh xèo', 'Bánh khọt', 'Bánh căn', 'Bánh tráng nướng', 'Bánh bèo'];
        foreach ($southernCakeItems as $item) {
            $categories[] = ['id' => $id++, 'name' => $item, 'description' => "$item miền Nam", 'parent_id' => $southernCakeParentId, 'status' => 'active', 'created_at' => '2024-01-01T00:00:00Z'];
        }

        // Tự động tạo code cho các categories chưa có code
        $categories = $this->ensureAllCategoriesHaveCode($categories);

        return array_slice($categories, 0, 125); // Đảm bảo chính xác 125 categories
    }

    /**
     * Đảm bảo tất cả categories đều có code
     */
    private function ensureAllCategoriesHaveCode(array $categories): array
    {
        $usedCodes = [];

        foreach ($categories as &$category) {
            if (empty($category['code'])) {
                // Tạo code từ tên
                $baseName = $category['name'];
                $code = $this->generateCodeFromName($baseName);

                // Đảm bảo code unique
                $originalCode = $code;
                $counter = 1;
                while (in_array($code, $usedCodes)) {
                    $code = $originalCode . '_' . str_pad($counter++, 2, '0', STR_PAD_LEFT);
                }

                $category['code'] = $code;
            }

            $usedCodes[] = $category['code'];
        }

        return $categories;
    }

    /**
     * Tạo code từ tên danh mục
     */
    private function generateCodeFromName(string $name): string
    {
        // Loại bỏ dấu tiếng Việt và ký tự đặc biệt
        $code = $this->removeVietnameseAccents($name);

        // Chỉ giữ lại chữ cái và số
        $code = preg_replace('/[^a-zA-Z0-9\s]/', '', $code);

        // Thay thế khoảng trắng bằng underscore và chuyển thành uppercase
        $code = strtoupper(str_replace(' ', '_', trim($code)));

        // Giới hạn độ dài
        return substr($code, 0, 12);
    }

    /**
     * Loại bỏ dấu tiếng Việt
     */
    private function removeVietnameseAccents(string $str): string
    {
        $accents = [
            'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
            'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
            'ì', 'í', 'ị', 'ỉ', 'ĩ',
            'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
            'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
            'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
            'đ'
        ];

        $replacements = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'i', 'i', 'i', 'i', 'i',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'y', 'y', 'y', 'y', 'y',
            'd'
        ];

        return str_replace($accents, $replacements, strtolower($str));
    }

    /**
     * Mock categories tree for testing - 125 categories
     */
    private function mockGetCategoriesTree(): array
    {
        $this->logInfo('Using mock categories tree');

        // Generate flat categories first
        $flatCategories = $this->generateBakeryCategories();

        // Convert to tree structure
        $categoriesTree = $this->buildCategoryTree($flatCategories);

        $totalCount = count($flatCategories);
        $treeDepth = $this->getTreeDepth($categoriesTree);

        return [
            'success' => true,
            'data' => [
                'categories_tree' => $categoriesTree,
                'categories_count' => $totalCount,
                'tree_depth' => $treeDepth
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Build category tree from flat array
     */
    private function buildCategoryTree(array $flatCategories): array
    {
        $tree = [];
        $lookup = [];

        // First pass: create lookup array and identify root categories
        foreach ($flatCategories as $category) {
            $treeNode = [
                'Id' => (string)$category['id'],
                'Code' => $category['code'] ?? '',
                'Name' => $category['name'],
                'Description' => $category['description'],
                'Grade' => $category['parent_id'] ? ($this->getCategoryDepth($category['id'], $flatCategories) + 1) : 1,
                'Inactive' => $category['status'] === 'inactive',
                'SortOrder' => $category['id'],
                'IsLeaf' => !$this->hasChildren($category['id'], $flatCategories),
                'Children' => []
            ];

            if ($category['parent_id']) {
                $treeNode['ParentId'] = (string)$category['parent_id'];
            }

            $lookup[$category['id']] = $treeNode;

            if (!$category['parent_id']) {
                $tree[] = &$lookup[$category['id']];
            }
        }

        // Second pass: build parent-child relationships
        foreach ($flatCategories as $category) {
            if ($category['parent_id'] && isset($lookup[$category['parent_id']])) {
                $lookup[$category['parent_id']]['Children'][] = &$lookup[$category['id']];
            }
        }

        return $tree;
    }

    /**
     * Check if category has children
     */
    private function hasChildren(int $categoryId, array $categories): bool
    {
        foreach ($categories as $category) {
            if ($category['parent_id'] === $categoryId) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get category depth in tree
     */
    private function getCategoryDepth(int $categoryId, array $categories): int
    {
        foreach ($categories as $category) {
            if ($category['id'] === $categoryId) {
                if (!$category['parent_id']) {
                    return 0;
                }
                return 1 + $this->getCategoryDepth($category['parent_id'], $categories);
            }
        }
        return 0;
    }

    /**
     * Get tree depth
     */
    private function getTreeDepth(array $tree): int
    {
        if (empty($tree)) {
            return 0;
        }

        $maxDepth = 0;
        foreach ($tree as $node) {
            $depth = 1;
            if (!empty($node['Children'])) {
                $depth += $this->getTreeDepth($node['Children']);
            }
            $maxDepth = max($maxDepth, $depth);
        }

        return $maxDepth;
    }

    /**
     * Đếm tổng số nodes trong tree
     */
    private function countTreeNodes(array $tree): int
    {
        $count = 0;
        foreach ($tree as $node) {
            $count++;
            if (isset($node['Children']) && is_array($node['Children'])) {
                $count += $this->countTreeNodes($node['Children']);
            }
        }
        return $count;
    }



    /**
     * Mock branchs for testing
     */
    private function mockGetBranchs(): array
    {
        $this->logInfo('Using mock branchs');

        $branchs = [
            [
                'Id' => 'dec16573-519d-405d-8299-3c111714f08c',
                'Code' => 'KHOTONG',
                'Name' => 'Kho tổng',
                'IsBaseDepot' => true,
                'IsChainBranch' => false,
                'ProvinceAddr' => 'VN101',
                'DistrictAddr' => 'VN10125',
                'CommuneAddr' => 'VN1012509',
                'Address' => '126 Tô Hiệu Hà Nội'
            ],
            [
                'Id' => '20d51699-a61d-4736-b965-946400b1b790',
                'Code' => 'CN_VUPHUC_01',
                'Name' => 'Chi nhánh Vũ Phúc 01',
                'IsBaseDepot' => false,
                'IsChainBranch' => false,
                'ProvinceAddr' => 'VN101',
                'DistrictAddr' => 'VN10125',
                'CommuneAddr' => 'VN1012509',
                'Address' => '123 Đường ABC, Quận XYZ, Hà Nội'
            ],
            [
                'Id' => '052e1ca4-00a4-4749-a56b-099bf2f3d707',
                'Code' => 'CN_VUPHUC_02',
                'Name' => 'Chi nhánh Vũ Phúc 02',
                'IsBaseDepot' => false,
                'IsChainBranch' => false,
                'ProvinceAddr' => 'VN101',
                'DistrictAddr' => 'VN10126',
                'CommuneAddr' => 'VN1012610',
                'Address' => '456 Đường DEF, Quận UVW, Hà Nội'
            ]
        ];

        return [
            'success' => true,
            'data' => [
                'branchs' => $branchs,
                'branchs_count' => count($branchs),
                'base_depot_count' => count(array_filter($branchs, fn($b) => $b['IsBaseDepot'])),
                'chain_branch_count' => count(array_filter($branchs, fn($b) => $b['IsChainBranch']))
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Mock customers for testing
     */
    private function mockGetCustomers(int $page = 1, int $limit = 10): array
    {
        $this->logInfo('Using mock customers', ['page' => $page, 'limit' => $limit]);

        $allCustomers = [
            [
                'Id' => '99bb83d7-a4c5-4d64-a152-b30d89433838',
                'Code' => 'KH000001',
                'Name' => 'Nguyễn Văn A',
                'Tel' => '0985858583',
                'NormalizedTel' => '84985858583',
                'StandardTel' => '0985858583',
                'Addr' => '123 Đường ABC, Quận 1',
                'Email' => 'nguyenvana@email.com',
                'Gender' => 0,
                'Description' => 'Khách hàng VIP',
                'IdentifyNumber' => '123456789',
                'ProvinceAddr' => 'Hà Nội',
                'DistrictAddr' => 'Quận Ba Đình',
                'CommuneAddr' => 'Phường Điện Biên',
                'MembershipCode' => 'VIP001',
                'MemberLevelID' => '97e6cf69-15da-4173-834b-d829551434a0',
                'MemberLevelName' => 'Vàng'
            ],
            [
                'Id' => '82c86a6e-11e5-4381-b379-5ea028bc5fb4',
                'Code' => 'KH000002',
                'Name' => 'Trần Thị B',
                'CustomerCategoryID' => '42654540-b7d0-468d-b404-157a51cc17d8',
                'CustomerCategoryName' => 'Khách lẻ',
                'Tel' => '0821571578',
                'NormalizedTel' => '84821571578',
                'StandardTel' => '0821571578',
                'Addr' => '456 Đường DEF, Quận 2',
                'Email' => 'tranthib@email.com',
                'Gender' => 1,
                'ProvinceAddr' => 'Hồ Chí Minh',
                'DistrictAddr' => 'Quận 1',
                'CommuneAddr' => 'Phường Bến Nghé',
                'MembershipCode' => 'REG002',
                'MemberLevelID' => '2305ab2a-6ee5-40c3-b615-7ca9ff31b3e2',
                'MemberLevelName' => 'Bạc'
            ],
            [
                'Id' => '12345678-1234-1234-1234-123456789012',
                'Code' => 'KH000003',
                'Name' => 'Lê Văn C',
                'Tel' => '0912345678',
                'NormalizedTel' => '84912345678',
                'StandardTel' => '0912345678',
                'Addr' => '789 Đường GHI, Quận 3',
                'Email' => 'levanc@email.com',
                'Gender' => 0,
                'ProvinceAddr' => 'Đà Nẵng',
                'DistrictAddr' => 'Quận Hải Châu',
                'CommuneAddr' => 'Phường Hải Châu 1',
                'MembershipCode' => 'STD003',
                'MemberLevelID' => '61f938f8-cb27-4806-92a4-8c1bbc2330f1',
                'MemberLevelName' => 'Đồng'
            ],
            [
                'Id' => 'adong-bakery-lap-vo-uuid-1234',
                'Code' => 'KH000004',
                'Name' => 'Á ĐÔNG Bakery - Lấp Vò (Anh Đô, chị Thi)',
                'Tel' => '0785125028',
                'NormalizedTel' => '84785125028',
                'StandardTel' => '0785125028',
                'Addr' => 'Lấp Vò, Đồng Tháp',
                'Email' => '',
                'Gender' => 0,
                'ProvinceAddr' => 'Đồng Tháp',
                'DistrictAddr' => 'Huyện Lấp Vò',
                'CommuneAddr' => 'Thị trấn Lấp Vò',
                'MembershipCode' => 'REG004',
                'MemberLevelID' => '2305ab2a-6ee5-40c3-b615-7ca9ff31b3e2',
                'MemberLevelName' => 'Bạc'
            ]
        ];

        // Simulate pagination
        $total = count($allCustomers);
        $offset = ($page - 1) * $limit;
        $customers = array_slice($allCustomers, $offset, $limit);

        return [
            'success' => true,
            'data' => [
                'customers' => $customers,
                'customers_count' => count($customers),
                'total_customers' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Mock member levels for testing
     */
    private function mockGetMemberLevels(int $page = 1, int $limit = 50): array
    {
        $this->logInfo('Using mock member levels', ['page' => $page, 'limit' => $limit]);

        $allMemberLevels = [
            [
                'MemberLevelID' => '97e6cf69-15da-4173-834b-d829551434a0',
                'MemberLevelName' => 'Vàng',
                'Description' => 'Hạng thẻ Vàng - Ưu đãi cao'
            ],
            [
                'MemberLevelID' => '2305ab2a-6ee5-40c3-b615-7ca9ff31b3e2',
                'MemberLevelName' => 'Bạc',
                'Description' => 'Hạng thẻ Bạc - Ưu đãi trung bình'
            ],
            [
                'MemberLevelID' => '61f938f8-cb27-4806-92a4-8c1bbc2330f1',
                'MemberLevelName' => 'Đồng',
                'Description' => 'Hạng thẻ Đồng - Ưu đãi cơ bản'
            ],
            [
                'MemberLevelID' => '12345678-1234-1234-1234-123456789012',
                'MemberLevelName' => 'Kim cương',
                'Description' => 'Hạng thẻ Kim cương - Ưu đãi tối đa'
            ],
            [
                'MemberLevelID' => '87654321-4321-4321-4321-210987654321',
                'MemberLevelName' => 'Bạch kim',
                'Description' => 'Hạng thẻ Bạch kim - Ưu đãi đặc biệt'
            ]
        ];

        // Simulate pagination
        $total = count($allMemberLevels);
        $offset = ($page - 1) * $limit;
        $memberLevels = array_slice($allMemberLevels, $offset, $limit);

        return [
            'success' => true,
            'data' => [
                'member_levels' => $memberLevels,
                'member_levels_count' => count($memberLevels),
                'total_member_levels' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ],
            'source' => 'mock'
        ];
    }

    /**
     * ========================================
     * CUSTOMER APIs - Tìm kiếm và thông tin khách hàng
     * ========================================
     */

    /**
     * Tìm kiếm danh sách khách hàng theo SĐT hoặc Email
     * API: /api/v1/customers/customerbyinfo
     */
    public function getCustomersByInfo(string $keySearch): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetCustomersByInfo($keySearch);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache
            $cacheKey = "mshopkeeper_customers_by_info_{$this->domain}_" . md5($keySearch);
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                $this->logInfo('Using cached customers by info');
                return [
                    'success' => true,
                    'data' => $cachedResult,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching customers by info from API', ['key_search' => $keySearch]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['customers_by_info'];

            // Tạo request body theo spec
            $requestBody = [
                'KeySearch' => $keySearch
            ];

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestBody);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Customers by info request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $customers = $responseData['Data'] ?? [];
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'customers' => $customers,
                    'customers_count' => count($customers),
                    'total_customers' => $total,
                    'key_search' => $keySearch,
                    'environment' => $responseData['Environment'] ?? $environment
                ];

                // Lưu vào cache (TTL ngắn hơn vì dữ liệu có thể thay đổi)
                Cache::put($cacheKey, $resultData, 300); // 5 phút

                $this->logInfo('Customers by info fetched successfully', [
                    'customers_count' => count($customers),
                    'total_customers' => $total,
                    'key_search' => $keySearch
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get customers by info failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get customers by info exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Tìm kiếm thông tin khách hàng Lomas theo SĐT hoặc mã thẻ thành viên
     * API: /api/v1/customers/search-lomas-info
     */
    public function searchLomasCustomerInfo(string $keyword): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockSearchLomasCustomerInfo($keyword);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache
            $cacheKey = "mshopkeeper_lomas_customer_{$this->domain}_" . md5($keyword);
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                $this->logInfo('Using cached Lomas customer info');
                return [
                    'success' => true,
                    'data' => $cachedResult,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching Lomas customer info from API', ['keyword' => $keyword]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['customers_lomas_search'];

            // Tạo request body theo spec
            $requestBody = [
                'Keyword' => $keyword
            ];

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestBody);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Lomas customer search request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $customerData = $responseData['Data'] ?? null;
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'customer' => $customerData['Customer'] ?? null,
                    'card_policies' => $customerData['CardPolicies'] ?? [],
                    'keyword' => $keyword,
                    'total' => $total,
                    'environment' => $responseData['Environment'] ?? $environment
                ];

                // Lưu vào cache (TTL ngắn hơn vì dữ liệu có thể thay đổi)
                Cache::put($cacheKey, $resultData, 300); // 5 phút

                $this->logInfo('Lomas customer info fetched successfully', [
                    'keyword' => $keyword,
                    'customer_found' => !empty($customerData['Customer']),
                    'card_policies_count' => count($customerData['CardPolicies'] ?? [])
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Search Lomas customer info failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Search Lomas customer info exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy danh sách điểm thẻ thành viên Lomas với phân trang
     * API: /api/v1/customers/point-paging
     */
    public function getCustomersPointPaging(int $page = 1, int $limit = 100): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetCustomersPointPaging($page, $limit);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache
            $cacheKey = "mshopkeeper_customers_point_paging_{$this->domain}_p{$page}_l{$limit}";
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                $this->logInfo('Using cached customers point paging');
                return [
                    'success' => true,
                    'data' => $cachedResult,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching customers point paging from API', ['page' => $page, 'limit' => $limit]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['customers_point_paging'];

            // Tạo request body theo spec
            $requestBody = [
                'Page' => $page,
                'Limit' => min($limit, 100) // Max 100 theo spec
            ];

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestBody);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Customers point paging request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $customerPoints = $responseData['Data'] ?? [];
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'customer_points' => $customerPoints,
                    'customer_points_count' => count($customerPoints),
                    'total_customer_points' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $limit > 0 ? ceil($total / $limit) : 0,
                    'environment' => $responseData['Environment'] ?? $environment
                ];

                // Lưu vào cache
                Cache::put($cacheKey, $resultData, $this->config['cache']['categories_ttl']);

                $this->logInfo('Customers point paging fetched successfully', [
                    'customer_points_count' => count($customerPoints),
                    'total_customer_points' => $total,
                    'page' => $page,
                    'limit' => $limit
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get customers point paging failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get customers point paging exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * ========================================
     * MOCK METHODS cho Customer APIs
     * ========================================
     */

    /**
     * Mock customers by info for testing
     */
    private function mockGetCustomersByInfo(string $keySearch): array
    {
        $this->logInfo('Using mock customers by info', ['key_search' => $keySearch]);

        // Simulate search logic - sử dụng key 'Id' để thống nhất với mockGetCustomers
        $allCustomers = [
            [
                'Id' => '99bb83d7-a4c5-4d64-a152-b30d89433838',
                'Code' => 'KH000012',
                'Name' => 'Văn Bình',
                'Tel' => '0985858583',
                'NormalizedTel' => '84985858583',
                'StandardTel' => '0985858583',
                'Addr' => '123 Đường ABC, Quận 1, TP.HCM',
                'Email' => 'vanbinh@email.com',
                'Gender' => 0,
                'CardName' => 'Vàng',
                'Point' => 150
            ],
            [
                'Id' => '82c86a6e-11e5-4381-b379-5ea028bc5fb4',
                'Code' => 'KH000013',
                'Name' => 'Trần Ngân',
                'Tel' => '0821571578',
                'NormalizedTel' => '84821571578',
                'StandardTel' => '0821571578',
                'Addr' => '02, ngõ 2, Hà Nội',
                'Email' => 'tranngan@email.com',
                'Gender' => 1,
                'CardName' => 'Bạc',
                'Point' => 85
            ],
            [
                'Id' => '12345678-1234-1234-1234-123456789012',
                'Code' => 'KH000014',
                'Name' => 'Nguyễn Phúc',
                'Tel' => '0987555222',
                'NormalizedTel' => '84987555222',
                'StandardTel' => '0987555222',
                'Addr' => '456 Đường DEF, Đà Nẵng',
                'Email' => 'nguyenphuc@email.com',
                'Gender' => 0,
                'CardName' => 'Đồng',
                'Point' => 50
            ],
            [
                'Id' => 'adong-bakery-lap-vo-uuid-1234',
                'Code' => 'KH000004',
                'Name' => 'Á ĐÔNG Bakery - Lấp Vò (Anh Đô, chị Thi)',
                'Tel' => '0785125028',
                'NormalizedTel' => '84785125028',
                'StandardTel' => '0785125028',
                'Addr' => 'Lấp Vò, Đồng Tháp',
                'Email' => '',
                'Gender' => 0,
                'CardName' => 'Kim cương',
                'Point' => 320
            ]
        ];

        // Filter customers based on keySearch (phone or email)
        $filteredCustomers = [];
        if (!empty($keySearch)) {
            $filteredCustomers = array_filter($allCustomers, function($customer) use ($keySearch) {
                return strpos($customer['Tel'], $keySearch) !== false ||
                       strpos($customer['Email'], $keySearch) !== false ||
                       strpos($customer['Name'], $keySearch) !== false;
            });
        }

        // Reset array keys
        $filteredCustomers = array_values($filteredCustomers);

        return [
            'success' => true,
            'data' => [
                'customers' => $filteredCustomers,
                'customers_count' => count($filteredCustomers),
                'total_customers' => count($filteredCustomers),
                'key_search' => $keySearch,
                'environment' => 'g1'
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Mock search Lomas customer info for testing
     */
    private function mockSearchLomasCustomerInfo(string $keyword): array
    {
        $this->logInfo('Using mock search Lomas customer info', ['keyword' => $keyword]);

        // Mock customer data based on keyword
        $mockCustomer = null;
        $mockCardPolicies = [];

        // Simulate finding customer by phone or card code
        if (in_array($keyword, ['0326643186', '100000131'])) {
            $mockCustomer = [
                'Id' => 132,
                'Code' => '100000131',
                'FullName' => 'Phụng Nghi 2344',
                'Tel' => '0326643186',
                'NormalizedTel' => '84326643186',
                'Email' => 'phungnghi@email.com',
                'Gender' => 1,
                'Address' => 'Xã Đồng Tháp,Huyện Đan Phượng,Hà Nội',
                'ProvinceName' => 'Hà Nội',
                'DistrictName' => 'Huyện Đan Phượng',
                'WardName' => 'Xã Đồng Tháp',
                'Street' => '108, võ thị sáu phường bình Tân, lagi, bình thuận',
                'Status' => true,
                'CompanyName' => '',
                'TaxCode' => '',
                'Note' => '',
                'IdentifyNumber' => '',
                'Avatar' => '',
                'CardId' => 2,
                'CardName' => 'Kim cương',
                'TotalPoint' => 638,
                'TotalAmount' => 167743158.0,
                'HardCardCode' => '',
                'OriginalId' => '002cca86-983a-4ac0-98e2-b226a89a16df',
                'EditMode' => 0,
                'Success' => true
            ];

            $mockCardPolicies = [
                [
                    'Id' => 0,
                    'Name' => 'Chính sách tích điểm',
                    'Type' => 1,
                    'ApplyFrom' => '2022-02-09T17:00:00Z',
                    'AddPointType' => 3,
                    'AddPointBy' => 3,
                    'UpgradeType' => 0,
                    'SpecialPolicy' => false,
                    'Amount' => 100000.0,
                    'Point' => 1.0,
                    'SpecialAmount' => 0.0,
                    'SpecialPoint' => 0.0,
                    'Rate' => 2.0,
                    'SpecialRate' => 0.0
                ],
                [
                    'Id' => 0,
                    'Name' => 'Chính sách đổi điểm',
                    'Type' => 2,
                    'ApplyFrom' => '2022-02-09T17:00:00Z',
                    'AddPointType' => 0,
                    'AddPointBy' => 0,
                    'UpgradeType' => 0,
                    'SpecialPolicy' => false,
                    'Amount' => 2000.0,
                    'Point' => 1.0,
                    'SpecialAmount' => 0.0,
                    'SpecialPoint' => 0.0,
                    'Rate' => 0.0,
                    'SpecialRate' => 0.0
                ]
            ];
        }

        return [
            'success' => true,
            'data' => [
                'customer' => $mockCustomer,
                'card_policies' => $mockCardPolicies,
                'keyword' => $keyword,
                'total' => $mockCustomer ? 1 : 0,
                'environment' => 'g1'
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Mock customers point paging for testing
     */
    private function mockGetCustomersPointPaging(int $page = 1, int $limit = 100): array
    {
        $this->logInfo('Using mock customers point paging', ['page' => $page, 'limit' => $limit]);

        $allCustomerPoints = [
            [
                'Tel' => '0985213213',
                'TotalPoint' => 159,
                'OriginalId' => 'f3ed5517-88cf-446d-874d-7e8e7e747924',
                'FullName' => 'KH Bình'
            ],
            [
                'Tel' => '02513830333',
                'TotalPoint' => 366,
                'OriginalId' => 'f3f35bb2-4253-4c7b-a8c9-7bc83bff3261',
                'FullName' => 'Nguyễn Hoài Sơn'
            ],
            [
                'Tel' => '0368159443',
                'TotalPoint' => 198,
                'OriginalId' => 'f5cd4bb8-9c3b-402c-a276-3c90b538a615',
                'FullName' => 'Thu Nguyễn'
            ],
            [
                'Tel' => '0987555222',
                'TotalPoint' => 425,
                'OriginalId' => '12345678-1234-1234-1234-123456789012',
                'FullName' => 'Nguyễn Phúc'
            ],
            [
                'Tel' => '0326643186',
                'TotalPoint' => 638,
                'OriginalId' => '002cca86-983a-4ac0-98e2-b226a89a16df',
                'FullName' => 'Phụng Nghi 2344'
            ],
            [
                'Tel' => '0985858583',
                'TotalPoint' => 150,
                'OriginalId' => '99bb83d7-a4c5-4d64-a152-b30d89433838',
                'FullName' => 'Văn Bình'
            ],
            [
                'Tel' => '0821571578',
                'TotalPoint' => 85,
                'OriginalId' => '82c86a6e-11e5-4381-b379-5ea028bc5fb4',
                'FullName' => 'Trần Ngân'
            ]
        ];

        // Simulate pagination
        $total = count($allCustomerPoints);
        $offset = ($page - 1) * $limit;
        $customerPoints = array_slice($allCustomerPoints, $offset, $limit);

        return [
            'success' => true,
            'data' => [
                'customer_points' => $customerPoints,
                'customer_points_count' => count($customerPoints),
                'total_customer_points' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit),
                'environment' => 'g1'
            ],
            'source' => 'mock'
        ];
    }

    /**
     * ========================================
     * INVENTORY/PRODUCTS API METHODS
     * ========================================
     */

    /**
     * Lấy danh sách hàng hóa với phân trang và chi tiết tồn kho
     * API: /api/v1/inventoryitems/pagingwithdetail
     */
    public function getInventoryItemsPagingWithDetail(array $params = []): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetInventoryItemsPagingWithDetail($params);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Default parameters
            $defaultParams = [
                'Page' => 1,
                'Limit' => 15,
                'SortField' => 'Name',
                'SortType' => 1,
                'IncludeInventory' => true,
                'IncludeInActive' => false,
            ];

            $requestParams = array_merge($defaultParams, $params);

            // Validate limit
            $requestParams['Limit'] = min($requestParams['Limit'], 100);

            // Kiểm tra cache
            $cacheKey = "mshopkeeper_inventory_paging_" . md5(json_encode($requestParams)) . "_{$this->domain}";
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                $this->logInfo('Using cached inventory items paging');
                return [
                    'success' => true,
                    'data' => $cachedResult,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching inventory items from API', $requestParams);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['inventory_paging_with_detail'];

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestParams);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Inventory items request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $inventoryItems = $responseData['Data'] ?? [];
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'inventory_items' => $inventoryItems,
                    'inventory_items_count' => count($inventoryItems),
                    'total_inventory_items' => $total,
                    'page' => $requestParams['Page'],
                    'limit' => $requestParams['Limit'],
                    'total_pages' => $requestParams['Limit'] > 0 ? ceil($total / $requestParams['Limit']) : 0,
                    'environment' => $responseData['Environment'] ?? $environment,
                    'params' => $requestParams
                ];

                // Lưu vào cache
                Cache::put($cacheKey, $resultData, $this->config['cache']['products_ttl']);

                $this->logInfo('Inventory items fetched successfully', [
                    'inventory_items_count' => count($inventoryItems),
                    'total_inventory_items' => $total,
                    'page' => $requestParams['Page'],
                    'limit' => $requestParams['Limit']
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get inventory items failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get inventory items exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Tìm kiếm hàng hóa theo mã
     * API: /api/v1/inventoryitems/pagingbycode
     */
    public function getInventoryItemsPagingByCode(string $codeSearch, int $page = 1, int $limit = 15): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetInventoryItemsPagingByCode($codeSearch, $page, $limit);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Validate limit
            $limit = min($limit, 100);

            // Kiểm tra cache
            $cacheKey = "mshopkeeper_inventory_search_{$this->domain}_" . md5($codeSearch) . "_p{$page}_l{$limit}";
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                $this->logInfo('Using cached inventory search');
                return [
                    'success' => true,
                    'data' => $cachedResult,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Searching inventory items by code', [
                'code_search' => $codeSearch,
                'page' => $page,
                'limit' => $limit
            ]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['inventory_paging_by_code'];

            $requestBody = [
                'Page' => $page,
                'Limit' => $limit,
                'CodeSearch' => $codeSearch
            ];

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestBody);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Inventory search request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $inventoryItems = $responseData['Data'] ?? [];
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'inventory_items' => $inventoryItems,
                    'inventory_items_count' => count($inventoryItems),
                    'total_inventory_items' => $total,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $limit > 0 ? ceil($total / $limit) : 0,
                    'code_search' => $codeSearch,
                    'environment' => $responseData['Environment'] ?? $environment
                ];

                // Lưu vào cache (TTL ngắn hơn vì search query)
                Cache::put($cacheKey, $resultData, 300); // 5 phút

                $this->logInfo('Inventory search completed successfully', [
                    'code_search' => $codeSearch,
                    'inventory_items_count' => count($inventoryItems),
                    'total_inventory_items' => $total
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Inventory search failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Inventory search exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Lấy chi tiết hàng hóa theo ID
     * API: /api/v1/inventoryitems/detail/{inventoryItemId}
     */
    public function getInventoryItemDetail(string $inventoryItemId): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetInventoryItemDetail($inventoryItemId);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return $authResult;
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Kiểm tra cache
            $cacheKey = "mshopkeeper_inventory_detail_{$this->domain}_{$inventoryItemId}";
            $cachedResult = Cache::get($cacheKey);

            if ($cachedResult) {
                $this->logInfo('Using cached inventory detail');
                return [
                    'success' => true,
                    'data' => $cachedResult,
                    'source' => 'cache'
                ];
            }

            $this->logInfo('Fetching inventory detail from API', ['inventory_item_id' => $inventoryItemId]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['inventory_detail'] . '/' . $inventoryItemId;

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->get($url);

            $responseData = $response->json();

            // Kiểm tra HTTP status code
            if ($response->status() === 401) {
                $this->logError('Inventory detail request failed - 401 Unauthorized');
                return [
                    'success' => false,
                    'error' => [
                        'type' => 'UNAUTHORIZED',
                        'message' => 'Chuỗi AccessToken hết hạn hoặc không hợp lệ',
                        'response' => $responseData
                    ]
                ];
            }

            // Kiểm tra ServiceResult format
            if ($response->successful() && isset($responseData['Success']) && $responseData['Success']) {
                $inventoryDetails = $responseData['Data'] ?? [];
                $total = $responseData['Total'] ?? 0;

                $resultData = [
                    'inventory_details' => $inventoryDetails,
                    'inventory_details_count' => count($inventoryDetails),
                    'total_inventory_details' => $total,
                    'inventory_item_id' => $inventoryItemId,
                    'environment' => $responseData['Environment'] ?? $environment
                ];

                // Lưu vào cache
                Cache::put($cacheKey, $resultData, $this->config['cache']['products_ttl']);

                $this->logInfo('Inventory detail fetched successfully', [
                    'inventory_item_id' => $inventoryItemId,
                    'inventory_details_count' => count($inventoryDetails)
                ]);

                return [
                    'success' => true,
                    'data' => $resultData,
                    'source' => 'api'
                ];
            }

            // Xử lý lỗi từ ServiceResult
            $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
            $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

            $this->logError('Get inventory detail failed', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response' => $responseData,
                'status_code' => $response->status()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Get inventory detail exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * ========================================
     * MOCK METHODS cho Inventory APIs
     * ========================================
     */

    /**
     * Mock inventory items paging with detail for testing
     */
    private function mockGetInventoryItemsPagingWithDetail(array $params = []): array
    {
        $this->logInfo('Using mock inventory items paging with detail', $params);

        $page = $params['Page'] ?? 1;
        $limit = $params['Limit'] ?? 15;

        $allInventoryItems = [
            [
                'Id' => '324d4a93-7508-4759-9a44-6174159d2cf8',
                'Code' => '12ARM001',
                'Name' => 'Bột baking soda Arm &Hammer 454g',
                'ItemType' => 1,
                'Barcode' => '033200011002',
                'SellingPrice' => 45000,
                'CostPrice' => 35000,
                'AvgUnitPrice' => 40000,
                'Color' => null,
                'Size' => '454g',
                'Material' => null,
                'Description' => 'Bột nở cao cấp',
                'IsItem' => false,
                'Inactive' => false,
                'UnitId' => 'unit-001',
                'UnitName' => 'Hộp',
                'Picture' => '/images/products/12ARM001.jpg',
                'ListDetail' => [
                    [
                        'Id' => 'child-001',
                        'ParentId' => '324d4a93-7508-4759-9a44-6174159d2cf8',
                        'Code' => '12ARM001-001',
                        'Name' => 'Bột baking soda Arm &Hammer 454g - Lô 1',
                        'IsItem' => true,
                        'SellingPrice' => 45000,
                        'CostPrice' => 35000,
                        'Inventories' => [
                            [
                                'ProductId' => 'child-001',
                                'ProductCode' => '12ARM001-001',
                                'ProductName' => 'Bột baking soda Arm &Hammer 454g - Lô 1',
                                'BranchId' => 'branch-001',
                                'BranchName' => 'Chi nhánh Quận 1',
                                'OnHand' => 150,
                                'Ordered' => 50,
                                'SellingPrice' => 45000
                            ],
                            [
                                'ProductId' => 'child-001',
                                'ProductCode' => '12ARM001-001',
                                'ProductName' => 'Bột baking soda Arm &Hammer 454g - Lô 1',
                                'BranchId' => 'branch-002',
                                'BranchName' => 'Chi nhánh Quận 3',
                                'OnHand' => 75,
                                'Ordered' => 25,
                                'SellingPrice' => 45000
                            ]
                        ]
                    ]
                ],
                'Inventories' => []
            ],
            [
                'Id' => '425e5b04-8619-4870-a055-7285270e3df9',
                'Code' => 'VPB001',
                'Name' => 'Bánh mì Vũ Phúc đặc biệt',
                'ItemType' => 1,
                'Barcode' => 'VPB001',
                'SellingPrice' => 25000,
                'CostPrice' => 15000,
                'AvgUnitPrice' => 20000,
                'Color' => 'Vàng',
                'Size' => 'Lớn',
                'Material' => null,
                'Description' => 'Bánh mì thủ công cao cấp',
                'IsItem' => true,
                'Inactive' => false,
                'UnitId' => 'unit-002',
                'UnitName' => 'Cái',
                'Picture' => '/images/products/VPB001.jpg',
                'ListDetail' => [],
                'Inventories' => [
                    [
                        'ProductId' => '425e5b04-8619-4870-a055-7285270e3df9',
                        'ProductCode' => 'VPB001',
                        'ProductName' => 'Bánh mì Vũ Phúc đặc biệt',
                        'BranchId' => 'branch-001',
                        'BranchName' => 'Chi nhánh Quận 1',
                        'OnHand' => 200,
                        'Ordered' => 100,
                        'SellingPrice' => 25000
                    ]
                ]
            ],
            [
                'Id' => '526f6c15-9720-4981-b166-8396381f4ea0',
                'Code' => 'COMBO01',
                'Name' => 'Combo bánh mì + nước',
                'ItemType' => 2,
                'Barcode' => 'COMBO01',
                'SellingPrice' => 35000,
                'CostPrice' => 25000,
                'AvgUnitPrice' => 30000,
                'Color' => null,
                'Size' => null,
                'Material' => null,
                'Description' => 'Combo tiết kiệm',
                'IsItem' => true,
                'Inactive' => false,
                'UnitId' => 'unit-003',
                'UnitName' => 'Combo',
                'Picture' => '/images/products/COMBO01.jpg',
                'ListDetail' => [],
                'Inventories' => [
                    [
                        'ProductId' => '526f6c15-9720-4981-b166-8396381f4ea0',
                        'ProductCode' => 'COMBO01',
                        'ProductName' => 'Combo bánh mì + nước',
                        'BranchId' => 'branch-001',
                        'BranchName' => 'Chi nhánh Quận 1',
                        'OnHand' => 50,
                        'Ordered' => 20,
                        'SellingPrice' => 35000
                    ]
                ]
            ]
        ];

        // Simulate pagination
        $total = count($allInventoryItems);
        $offset = ($page - 1) * $limit;
        $paginatedItems = array_slice($allInventoryItems, $offset, $limit);

        return [
            'success' => true,
            'data' => [
                'inventory_items' => $paginatedItems,
                'inventory_items_count' => count($paginatedItems),
                'total_inventory_items' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit),
                'environment' => 'g1',
                'params' => $params
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Mock inventory items search by code for testing
     */
    private function mockGetInventoryItemsPagingByCode(string $codeSearch, int $page = 1, int $limit = 15): array
    {
        $this->logInfo('Using mock inventory search by code', [
            'code_search' => $codeSearch,
            'page' => $page,
            'limit' => $limit
        ]);

        $allInventoryItems = [
            [
                'Id' => '324d4a93-7508-4759-9a44-6174159d2cf8',
                'Code' => '12ARM001',
                'Name' => 'Bột baking soda Arm &Hammer 454g',
                'ItemType' => 1,
                'SellingPrice' => 45000,
                'CostPrice' => 35000,
                'IsItem' => false,
                'Inactive' => false
            ],
            [
                'Id' => '425e5b04-8619-4870-a055-7285270e3df9',
                'Code' => 'VPB001',
                'Name' => 'Bánh mì Vũ Phúc đặc biệt',
                'ItemType' => 1,
                'SellingPrice' => 25000,
                'CostPrice' => 15000,
                'IsItem' => true,
                'Inactive' => false
            ]
        ];

        // Filter by code search
        $filteredItems = array_filter($allInventoryItems, function($item) use ($codeSearch) {
            return stripos($item['Code'], $codeSearch) !== false || stripos($item['Name'], $codeSearch) !== false;
        });

        // Simulate pagination
        $total = count($filteredItems);
        $offset = ($page - 1) * $limit;
        $paginatedItems = array_slice($filteredItems, $offset, $limit);

        return [
            'success' => true,
            'data' => [
                'inventory_items' => array_values($paginatedItems),
                'inventory_items_count' => count($paginatedItems),
                'total_inventory_items' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit),
                'code_search' => $codeSearch,
                'environment' => 'g1'
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Mock inventory item detail for testing
     */
    private function mockGetInventoryItemDetail(string $inventoryItemId): array
    {
        $this->logInfo('Using mock inventory detail', ['inventory_item_id' => $inventoryItemId]);

        $mockDetails = [
            [
                'Id' => 'child-001',
                'ParentId' => $inventoryItemId,
                'Code' => '12ARM001-001',
                'Name' => 'Bột baking soda Arm &Hammer 454g - Lô 1',
                'IsItem' => true,
                'SellingPrice' => 45000,
                'CostPrice' => 35000,
                'Inventories' => [
                    [
                        'ProductId' => 'child-001',
                        'ProductCode' => '12ARM001-001',
                        'ProductName' => 'Bột baking soda Arm &Hammer 454g - Lô 1',
                        'BranchId' => 'branch-001',
                        'BranchName' => 'Chi nhánh Quận 1',
                        'OnHand' => 150,
                        'Ordered' => 50,
                        'SellingPrice' => 45000
                    ],
                    [
                        'ProductId' => 'child-001',
                        'ProductCode' => '12ARM001-001',
                        'ProductName' => 'Bột baking soda Arm &Hammer 454g - Lô 1',
                        'BranchId' => 'branch-002',
                        'BranchName' => 'Chi nhánh Quận 3',
                        'OnHand' => 75,
                        'Ordered' => 25,
                        'SellingPrice' => 45000
                    ]
                ]
            ]
        ];

        return [
            'success' => true,
            'data' => [
                'inventory_details' => $mockDetails,
                'inventory_details_count' => count($mockDetails),
                'total_inventory_details' => count($mockDetails),
                'inventory_item_id' => $inventoryItemId,
                'environment' => 'g1'
            ],
            'source' => 'mock'
        ];
    }

    /**
     * Tạo khách hàng mới trong MShopKeeper
     * API: POST /api/v1/customers/
     */
    public function createCustomer($customerData): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockCreateCustomer($customerData);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                throw new \Exception('Authentication failed: ' . $authResult['error']['message']);
            }

            $accessToken = $authResult['data']['access_token'];
            $environment = $authResult['data']['environment'];
            $companyCode = $authResult['data']['company_code'];

            $this->logInfo('Creating customer in MShopKeeper', ['customer_data' => $customerData]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['create_customer'];

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $customerData);

            if ($response->successful()) {
                $responseData = $response->json();

                if ($responseData['Success'] && $responseData['Code'] == 200) {
                    $this->logInfo('Customer created successfully', [
                        'customer_id' => $responseData['Data']['Id'],
                        'customer_code' => $responseData['Data']['Code']
                    ]);

                    return [
                        'success' => true,
                        'data' => $responseData['Data']
                    ];
                } else {
                    // Xử lý trường hợp khách hàng đã tồn tại (ErrorType = 200)
                    if ($responseData['ErrorType'] == 200) {
                        $this->logInfo('Customer already exists', [
                            'existing_customer' => $responseData['Data']
                        ]);

                        return [
                            'success' => false,
                            'error' => [
                                'type' => 'CUSTOMER_EXISTS',
                                'message' => $responseData['ErrorMessage'],
                                'existing_customer' => $responseData['Data']
                            ]
                        ];
                    }

                    // Các lỗi khác
                    $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
                    $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

                    $this->logError('Create customer failed', [
                        'error_type' => $errorType,
                        'error_message' => $errorMessage,
                        'response' => $responseData
                    ]);

                    return [
                        'success' => false,
                        'error' => [
                            'type' => $errorType,
                            'message' => $errorMessage,
                            'response' => $responseData
                        ]
                    ];
                }
            } else {
                throw new \Exception('HTTP Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            $this->logError('Create customer exception', [
                'message' => $e->getMessage(),
                'customer_data' => $customerData,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Mock create customer for testing
     */
    private function mockCreateCustomer($customerData): array
    {
        $this->logInfo('Using mock create customer');

        // Simulate customer already exists scenario
        if ($customerData['Tel'] === '0123456789') {
            return [
                'success' => false,
                'error' => [
                    'type' => 'CUSTOMER_EXISTS',
                    'message' => 'Số điện thoại đã tồn tại',
                    'existing_customer' => [
                        'Id' => 'existing-customer-id',
                        'Code' => 'KH000123',
                        'Name' => 'Khách hàng cũ',
                        'Tel' => '0123456789'
                    ]
                ]
            ];
        }

        // Simulate successful creation
        $mockCustomer = [
            'Id' => 'mock-customer-' . uniqid(),
            'Code' => 'KH' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT),
            'Name' => $customerData['Name'],
            'Tel' => $customerData['Tel'],
            'Email' => $customerData['Email'] ?? null,
            'Addr' => $customerData['Addr'] ?? null,
            'Gender' => $customerData['Gender'] ?? 0
        ];

        return [
            'success' => true,
            'data' => $mockCustomer
        ];
    }

    /**
     * Lấy danh sách chi nhánh
     * API: POST /api/v1/branchs/all
     */
    public function getBranches($params = []): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetBranches($params);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return [
                    'success' => false,
                    'error' => [
                        'type' => 401,
                        'message' => 'Authentication failed: ' . $authResult['message']
                    ]
                ];
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            // Default params
            $requestParams = array_merge([
                'IsIncludeInactiveBranch' => false,
                'IsIncludeChainOfBranch' => false
            ], $params);

            $this->logInfo('Getting branches from MShopKeeper', [
                'params' => $requestParams,
                'environment' => $environment,
                'company_code' => $companyCode
            ]);

            // Gọi API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'CompanyCode' => $companyCode,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/' . $environment . '/api/v1/branchs/all', $requestParams);

            $statusCode = $response->status();
            $responseData = $response->json();

            $this->logInfo('Branches API response', [
                'status' => $statusCode,
                'success' => $responseData['Success'] ?? false,
                'error_type' => $responseData['ErrorType'] ?? null,
                'branches_count' => is_array($responseData['Data'] ?? null) ? count($responseData['Data']) : 0
            ]);

            if ($statusCode === 200 && ($responseData['Success'] ?? false)) {
                return [
                    'success' => true,
                    'data' => $responseData['Data'] ?? []
                ];
            } else {
                $this->logError('Get branches failed', [
                    'error_type' => $responseData['ErrorType'] ?? null,
                    'error_message' => $responseData['ErrorMessage'] ?? 'Unknown error',
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'error' => [
                        'type' => $responseData['ErrorType'] ?? 100,
                        'message' => $responseData['ErrorMessage'] ?? 'Get branches failed',
                        'response' => $responseData
                    ]
                ];
            }

        } catch (\Exception $e) {
            $this->logError('Get branches exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 100,
                    'message' => 'Exception: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Mock get branches for testing
     */
    private function mockGetBranches($params): array
    {
        $this->logInfo('Using mock get branches');

        return [
            'success' => true,
            'data' => [
                [
                    'Id' => 'dec16573-519d-405d-8299-3c111714f08c',
                    'Code' => 'KHOTONG',
                    'Name' => 'Kho tổng',
                    'IsBaseDepot' => true,
                    'IsChainBranch' => false,
                    'ProvinceAddr' => 'VN101',
                    'DistrictAddr' => 'VN10125',
                    'CommuneAddr' => 'VN1012509',
                    'Address' => '126 Tô Hiệu Hà Nội'
                ],
                [
                    'Id' => '20d51699-a61d-4736-b965-946400b1b790',
                    'Code' => '267HATHIEN',
                    'Name' => '267 Hà Thiên',
                    'IsBaseDepot' => false,
                    'IsChainBranch' => false,
                    'ProvinceAddr' => '',
                    'DistrictAddr' => '',
                    'CommuneAddr' => '',
                    'Address' => '56 Giảng Võ Hà Nội'
                ]
            ]
        ];
    }

    /**
     * Tạo đơn hàng trong MShopKeeper với OrderNo có prefix WEB_
     * API: POST /api/v1/invoices/
     */
    public function createOrder($orderData): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockCreateOrder($orderData);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                throw new \Exception('Authentication failed: ' . $authResult['error']['message']);
            }

            $accessToken = $authResult['data']['access_token'];
            $environment = $authResult['data']['environment'];
            $companyCode = $authResult['data']['company_code'];

            // KHÔNG gửi OrderNo để hệ thống MShopKeeper tự sinh với format DT000xxx
            // Theo API docs: OrderNo không bắt buộc, hệ thống sẽ tự sinh
            if (isset($orderData['OrderNo'])) {
                unset($orderData['OrderNo']);
            }

            $this->logInfo('Creating order in MShopKeeper', [
                'order_no' => 'Auto-generated by MShopKeeper',
                'total_amount' => $orderData['TotalAmount'] ?? 0
            ]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['create_order'];

            // Gửi request với headers theo spec
            $response = Http::timeout(60) // Tăng timeout cho order API
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $orderData);

            if ($response->successful()) {
                $responseData = $response->json();

                if ($responseData['Success'] && $responseData['Code'] == 200) {
                    $this->logInfo('Order created successfully', [
                        'order_id' => $responseData['Data']['OrderId'],
                        'order_no' => $responseData['Data']['OrderNo']
                    ]);

                    return [
                        'success' => true,
                        'data' => $responseData['Data']
                    ];
                } else {
                    $errorType = $responseData['ErrorType'] ?? 'API_ERROR';
                    $errorMessage = $responseData['ErrorMessage'] ?? $this->getErrorMessage($errorType);

                    $this->logError('Create order failed', [
                        'error_type' => $errorType,
                        'error_message' => $errorMessage,
                        'response' => $responseData
                    ]);

                    return [
                        'success' => false,
                        'error' => [
                            'type' => $errorType,
                            'message' => $errorMessage,
                            'response' => $responseData
                        ]
                    ];
                }
            } else {
                throw new \Exception('HTTP Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            $this->logError('Create order exception', [
                'message' => $e->getMessage(),
                'order_data' => $orderData,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Mock create order for testing
     */
    private function mockCreateOrder($orderData): array
    {
        $this->logInfo('Using mock create order');

        $mockOrder = $orderData;
        $mockOrder['OrderId'] = 'mock-order-' . uniqid();
        $mockOrder['OrderNo'] = $orderData['OrderNo'] ?? 'WEB_' . time();

        return [
            'success' => true,
            'data' => $mockOrder
        ];
    }

    /**
     * Lấy danh sách đơn hàng từ MShopKeeper với phân trang
     * API: POST /api/v1/orders/list
     */
    public function getOrders(array $requestParams): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetOrders($requestParams);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                return [
                    'success' => false,
                    'error' => [
                        'type' => 401,
                        'message' => 'Authentication failed: ' . $authResult['message']
                    ]
                ];
            }

            $accessToken = $authResult['data']['access_token'];
            $companyCode = $authResult['data']['company_code'];
            $environment = $authResult['data']['environment'];

            $this->logInfo('Getting orders from MShopKeeper', [
                'params' => $requestParams,
                'environment' => $environment,
                'company_code' => $companyCode
            ]);

            // Gọi API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'CompanyCode' => $companyCode,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/' . $environment . '/api/v1/orders/list', $requestParams);

            $statusCode = $response->status();
            $responseData = $response->json();

            $this->logInfo('Orders API response', [
                'status' => $statusCode,
                'success' => $responseData['Success'] ?? false,
                'error_type' => $responseData['ErrorType'] ?? null,
                'orders_count' => is_array($responseData['Data'] ?? null) ? count($responseData['Data']) : 0
            ]);

            if ($statusCode === 200 && ($responseData['Success'] ?? false)) {
                return [
                    'success' => true,
                    'data' => $responseData['Data'] ?? []
                ];
            } else {
                $this->logError('Get orders failed', [
                    'error_type' => $responseData['ErrorType'] ?? null,
                    'error_message' => $responseData['ErrorMessage'] ?? 'Unknown error',
                    'response' => $responseData
                ]);

                return [
                    'success' => false,
                    'error' => [
                        'type' => $responseData['ErrorType'] ?? 100,
                        'message' => $responseData['ErrorMessage'] ?? 'Get orders failed',
                        'response' => $responseData
                    ]
                ];
            }

        } catch (\Exception $e) {
            $this->logError('Get orders exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 100,
                    'message' => 'Exception: ' . $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Mock get orders for testing
     */
    private function mockGetOrders($requestParams): array
    {
        $this->logInfo('Using mock get orders');

        return [
            'success' => true,
            'data' => [
                [
                    'OrderId' => 'dd4a1745-4d42-48d9-aeb7-a202294983b2',
                    'OrderNo' => 'DT000025',
                    'OrderDate' => '2025-08-19T05:30:00+07:00',
                    'TotalAmount' => 7000.0,
                    'Status' => 'Pending',
                    'Customer' => [
                        'Id' => 'debug-orderno-1755556000',
                        'Code' => 'DEBUG_ORDERNO_CUSTOMER',
                        'Name' => 'Debug OrderNo Customer',
                        'Tel' => '0999888777',
                        'Email' => 'test-orderno@debug.com'
                    ],
                    'OrderDetails' => [
                        [
                            'ProductId' => '324d4a93-7508-4759-9a44-6174159d2cf8',
                            'ProductCode' => '188108207',
                            'ProductName' => 'Bàn chải chà khe hở, góc chết - giá 1:10',
                            'Quantity' => 1.0,
                            'SellingPrice' => 7000.0,
                            'Amount' => 7000.0
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Lấy danh sách hóa đơn từ MShopKeeper với phân trang
     * API: POST /api/v1/invoices/pagingbycustomer
     */
    public function getInvoicesPaging(array $requestParams): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetInvoicesPaging($requestParams);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                throw new \Exception('Authentication failed: ' . $authResult['error']['message']);
            }

            $accessToken = $authResult['data']['access_token'];
            $environment = $authResult['data']['environment'];
            $companyCode = $authResult['data']['company_code'];

            $this->logInfo('Getting invoices paging from MShopKeeper', [
                'page' => $requestParams['Page'] ?? 1,
                'limit' => $requestParams['Limit'] ?? 100,
                'from_date' => $requestParams['FromDate'] ?? null,
                'to_date' => $requestParams['ToDate'] ?? null,
            ]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . '/api/v1/invoices/pagingbycustomer';

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestParams);

            if ($response->successful()) {
                $responseData = $response->json();

                // Check for API errors
                if (!$responseData['Success']) {
                    $errorType = $responseData['ErrorType'] ?? 'unknown';
                    $errorMessage = $responseData['ErrorMessage'] ?? 'Unknown error';

                    throw new \Exception("API Error (Type: {$errorType}): {$errorMessage}");
                }

                $this->logInfo('Invoices paging retrieved successfully', [
                    'total' => $responseData['Total'] ?? 0,
                    'data_count' => count($responseData['Data'] ?? [])
                ]);

                return [
                    'success' => true,
                    'data' => $responseData
                ];

            } else {
                throw new \Exception('HTTP Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            $this->logError('Get invoices paging exception', [
                'message' => $e->getMessage(),
                'request_params' => $requestParams,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Mock get invoices paging for testing
     */
    private function mockGetInvoicesPaging(array $requestParams): array
    {
        $this->logInfo('Using mock get invoices paging');

        $page = $requestParams['Page'] ?? 1;
        $limit = $requestParams['Limit'] ?? 100;

        // Generate mock invoices
        $mockInvoices = [];
        $totalMockInvoices = 150; // Total mock invoices

        $startIndex = ($page - 1) * $limit;
        $endIndex = min($startIndex + $limit, $totalMockInvoices);

        for ($i = $startIndex; $i < $endIndex; $i++) {
            $invoiceNumber = 'Web' . str_pad($i + 1, 6, '0', STR_PAD_LEFT);
            $mockInvoices[] = [
                'InvoiceId' => 'mock-invoice-' . ($i + 1),
                'InvoiceNumber' => $invoiceNumber,
                'InvoiceType' => 550,
                'InvoiceDate' => now()->subDays(rand(0, 30))->toISOString(),
                'InvoiceTime' => now()->subDays(rand(0, 30))->toISOString(),
                'BranchId' => '8C3D6B0D-3B58-4379-BFFB-1CCEA7A7F884',
                'BranchName' => 'Chi nhánh chính',
                'TotalAmount' => rand(100000, 5000000),
                'CostAmount' => 0,
                'TaxAmount' => 0,
                'TotalItemAmount' => rand(100000, 5000000),
                'VATAmount' => 0,
                'DiscountAmount' => rand(0, 100000),
                'CashAmount' => rand(100000, 5000000),
                'CardAmount' => 0,
                'VoucherAmount' => 0,
                'DebitAmount' => 0,
                'ActualAmount' => rand(100000, 5000000),
                'CustomerName' => 'Khách hàng ' . ($i + 1),
                'Tel' => '098' . str_pad($i + 1, 7, '0', STR_PAD_LEFT),
                'Address' => 'Địa chỉ khách hàng ' . ($i + 1),
                'Cashier' => 'Thu ngân',
                'SaleStaff' => 'Nhân viên bán hàng',
                'PaymentStatus' => rand(1, 10),
                'IsCOD' => rand(0, 1) === 1,
                'SaleChannelName' => ['Website', 'Facebook', 'Shopee'][rand(0, 2)],
                'DeliveryCode' => 'DLV' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'ShippingPartnerName' => ['Giao hàng nhanh', 'J&T Express', 'Viettel Post'][rand(0, 2)],
                'PartnerStatus' => rand(1, 3),
                'Point' => rand(0, 100),
            ];
        }

        return [
            'success' => true,
            'data' => [
                'Code' => 200,
                'Data' => $mockInvoices,
                'Total' => $totalMockInvoices,
                'Success' => true,
                'Environment' => 'g1'
            ]
        ];
    }

    /**
     * ========================================
     * INVOICE DETAIL API METHODS
     * ========================================
     */

    /**
     * Lấy chi tiết hóa đơn theo RefID
     * API: POST /api/v1/invoices/detailbyrefid
     */
    public function getInvoiceDetailByRefId(string $refId): array
    {
        // Mock mode for testing
        if ($this->config['mock_mode']) {
            return $this->mockGetInvoiceDetailByRefId($refId);
        }

        try {
            // Lấy access token
            $authResult = $this->authenticate();
            if (!$authResult['success']) {
                throw new \Exception('Authentication failed: ' . $authResult['error']['message']);
            }

            $accessToken = $authResult['data']['access_token'];
            $environment = $authResult['data']['environment'];
            $companyCode = $authResult['data']['company_code'];

            $requestParams = [
                'RefID' => $refId
            ];

            $this->logInfo('Getting invoice detail by RefID from MShopKeeper', [
                'ref_id' => $refId,
                'environment' => $environment,
                'company_code' => $companyCode
            ]);

            // Build URL với Environment
            $url = $this->baseUrl . '/' . $environment . $this->config['endpoints']['invoice_detail_by_refid'];

            // Gửi request với headers theo spec
            $response = Http::timeout($this->config['timeout'])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'CompanyCode' => $companyCode,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($url, $requestParams);

            $responseData = $response->json();

            $this->logInfo('Invoice detail API response', [
                'status' => $response->status(),
                'success' => $responseData['Success'] ?? false,
                'error_type' => $responseData['ErrorType'] ?? null,
                'has_data' => isset($responseData['Data']),
                'invoice_details_count' => isset($responseData['Data']['InvocieDetails']) ? count($responseData['Data']['InvocieDetails']) : 0
            ]);

            if ($response->successful() && ($responseData['Success'] ?? false)) {
                return [
                    'success' => true,
                    'data' => $responseData['Data'],
                    'environment' => $responseData['Environment'] ?? $environment,
                    'source' => 'api'
                ];
            }

            // Handle API errors
            $errorType = $responseData['ErrorType'] ?? 'UNKNOWN';
            $errorMessage = $responseData['ErrorMessage'] ?? 'Unknown error occurred';

            $this->logError('Invoice detail API error', [
                'error_type' => $errorType,
                'error_message' => $errorMessage,
                'response_data' => $responseData
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'response' => $responseData
                ]
            ];

        } catch (\Exception $e) {
            $this->logError('Invoice detail exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => 'Lỗi kết nối: ' . $e->getMessage(),
                    'exception' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Mock invoice detail for testing
     */
    private function mockGetInvoiceDetailByRefId(string $refId): array
    {
        $this->logInfo('Using mock invoice detail by RefID', ['ref_id' => $refId]);

        $mockData = [
            'AmountReturn' => 0.0,
            'DiscountAmountReturn' => 0.0,
            'TotalAmountReturn' => 0.0,
            'TotalItemAmount' => 170000.0,
            'DiscountAmount' => 0.0,
            'TotalItemDiscountAmount' => 0.0,
            'DeliveryAmount' => 0.0,
            'VATAmount' => 0.0,
            'DepositAmount' => 0.0,
            'VoucherAmount' => 0.0,
            'CashAmount' => 170000.0,
            'CardAmount' => 0.0,
            'NotTakeChangeAmount' => 0.0,
            'ChangeDeductedAmount' => 0.0,
            'DebitAmount' => 0.0,
            'ReturnCashAmount' => 0.0,
            'ReturnCardAmount' => 0.0,
            'ChangeAmount' => 0.0,
            'ReturnExchangeAmount' => 0.0,
            'ReduceDebtAmount' => 0.0,
            'InvoiceType' => 550,
            'InvoiceNumber' => '2290820357',
            'OrderNumber' => 'DT000001',
            'InvoiceDate' => '2025-01-18T16:41:11.643+07:00',
            'InvoiceTime' => '2025-01-18T16:41:11.643+07:00',
            'BranchId' => '8c3d6b0d-3b58-4379-bffb-1ccea7a7f884',
            'PaymentStatus' => 3,
            'InvoiceDetailType' => 0,
            'Cashier' => 'Nguyễn Văn Anh',
            'SaleStaff' => 'Nguyễn Văn Anh',
            'CustomerName' => 'CÔNG TY TNHH SX TM DV VŨ PHÚC',
            'Tel' => '0924792481',
            'DeliveryAddress' => '125/8 Hàm 4 đường Nguyễn Việt Hồng, phường An Phú, huyện Kiến, Cần Thơ',
            'IsCOD' => false,
            'IsDebit' => false,
            'AdditionBillType' => 0,
            'Point' => 0.0,
            'InvocieDetails' => [
                [
                    'InvoiceDetailType' => 1,
                    'SKU' => 'BM001',
                    'Name' => 'Bánh mì sandwich thịt nguội',
                    'EncodeInventoryItemName' => 'Bánh mì sandwich thịt nguội',
                    'Quantity' => 2.0,
                    'UnitPrice' => 25000.0,
                    'Amount' => 50000.0,
                    'TotalAmount' => 50000.0,
                    'DiscountAmount' => 0.0,
                    'SortOrder' => 1,
                    'UnitName' => 'Chiếc'
                ],
                [
                    'InvoiceDetailType' => 1,
                    'SKU' => 'NC001',
                    'Name' => 'Nước cam tươi',
                    'EncodeInventoryItemName' => 'Nước cam tươi',
                    'Quantity' => 1.0,
                    'UnitPrice' => 15000.0,
                    'Amount' => 15000.0,
                    'TotalAmount' => 15000.0,
                    'DiscountAmount' => 0.0,
                    'SortOrder' => 2,
                    'UnitName' => 'Ly'
                ],
                [
                    'InvoiceDetailType' => 1,
                    'SKU' => 'BN001',
                    'Name' => 'Bánh ngọt chocolate',
                    'EncodeInventoryItemName' => 'Bánh ngọt chocolate',
                    'Quantity' => 3.0,
                    'UnitPrice' => 35000.0,
                    'Amount' => 105000.0,
                    'TotalAmount' => 105000.0,
                    'DiscountAmount' => 0.0,
                    'SortOrder' => 3,
                    'UnitName' => 'Chiếc'
                ]
            ]
        ];

        return [
            'success' => true,
            'data' => $mockData,
            'environment' => 'g1',
            'source' => 'mock'
        ];
    }
}
