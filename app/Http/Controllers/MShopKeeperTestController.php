<?php

namespace App\Http\Controllers;

use App\Services\MShopKeeperService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class MShopKeeperTestController extends Controller
{
    private MShopKeeperService $mshopkeeperService;

    public function __construct(MShopKeeperService $mshopkeeperService)
    {
        $this->mshopkeeperService = $mshopkeeperService;
    }

    /**
     * Trang test tổng quan
     */
    public function index(): JsonResponse
    {
        $config = config('mshopkeeper');
        
        return response()->json([
            'title' => 'MShopKeeper API Test Dashboard',
            'timestamp' => Carbon::now()->toISOString(),
            'config' => [
                'app_id' => $config['app_id'],
                'domain' => $config['domain'],
                'base_url' => $config['base_url'],
                'timeout' => $config['timeout'],
                'endpoints' => $config['endpoints']
            ],
            'available_tests' => [
                'authentication' => '/test-mshopkeeper/auth',
                'categories' => '/test-mshopkeeper/categories',
                'categories_tree' => '/test-mshopkeeper/categories-tree',
                'branchs' => '/test-mshopkeeper/branchs',
                'customers' => '/test-mshopkeeper/customers',
                'member_levels' => '/test-mshopkeeper/member-levels',

                // Customer APIs - Tìm kiếm và thông tin khách hàng
                'customers_by_info' => '/test-mshopkeeper/customers-by-info',
                'customers_lomas_search' => '/test-mshopkeeper/customers-lomas-search',
                'customers_point_paging' => '/test-mshopkeeper/customers-point-paging',

                'full_test' => '/test-mshopkeeper/full-test',
                'clear_cache' => '/test-mshopkeeper/clear-cache'
            ],
            'instructions' => [
                '1. Kiểm tra authentication trước',
                '2. Test lấy categories',
                '3. Chạy full test để kiểm tra toàn bộ flow',
                '4. Sử dụng clear-cache để xóa cache khi cần'
            ]
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Test authentication
     */
    public function testAuthentication(): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            $result = $this->mshopkeeperService->authenticate();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Authentication',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
            ];

            if ($result['success']) {
                $response['data'] = [
                    'access_token_length' => strlen($result['data']['access_token']),
                    'access_token_preview' => substr($result['data']['access_token'], 0, 20) . '...',
                    'expires_at' => $result['data']['expires_at'],
                    'domain' => $result['data']['domain'],
                    'source' => $result['source']
                ];
                $response['message'] = 'Authentication thành công';
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Authentication thất bại';
            }

            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($response, $statusCode, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return response()->json([
                'test' => 'Authentication',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'message' => 'Lỗi exception trong quá trình test authentication'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test lấy categories
     */
    public function testCategories(): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $result = $this->mshopkeeperService->getCategories();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Get Categories (Flat)',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
            ];

            if ($result['success']) {
                $categories = $result['data'];
                $response['data'] = [
                    'categories_count' => is_array($categories) ? count($categories) : 0,
                    'categories' => $categories,
                    'source' => $result['source']
                ];
                $response['message'] = 'Lấy categories (flat) thành công';
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Lấy categories (flat) thất bại';
            }

            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($response, $statusCode, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'test' => 'Get Categories (Flat)',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'message' => 'Lỗi exception trong quá trình test categories (flat)'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test lấy categories tree
     */
    public function testCategoriesTree(): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $result = $this->mshopkeeperService->getCategoriesTree();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Get Categories Tree',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
            ];

            if ($result['success']) {
                $data = $result['data'];
                $response['data'] = [
                    'categories_count' => $data['categories_count'] ?? 0,
                    'tree_depth' => $data['tree_depth'] ?? 0,
                    'root_categories' => is_array($data['categories_tree']) ? count($data['categories_tree']) : 0,
                    'categories_tree' => $data['categories_tree'] ?? [],
                    'source' => $result['source']
                ];
                $response['message'] = 'Lấy categories tree thành công';
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Lấy categories tree thất bại';
            }

            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($response, $statusCode, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'test' => 'Get Categories Tree',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'message' => 'Lỗi exception trong quá trình test categories tree'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test lấy branchs
     */
    public function testBranchs(): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $result = $this->mshopkeeperService->getBranchs();
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Get Branchs',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
            ];

            if ($result['success']) {
                $data = $result['data'];
                $response['data'] = [
                    'branchs_count' => $data['branchs_count'] ?? 0,
                    'base_depot_count' => $data['base_depot_count'] ?? 0,
                    'chain_branch_count' => $data['chain_branch_count'] ?? 0,
                    'branchs' => $data['branchs'] ?? [],
                    'source' => $result['source']
                ];
                $response['message'] = 'Lấy branchs thành công';
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Lấy branchs thất bại';
            }

            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($response, $statusCode, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'test' => 'Get Branchs',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'message' => 'Lỗi exception trong quá trình test branchs'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test lấy customers
     */
    public function testCustomers(): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $result = $this->mshopkeeperService->getCustomers(1, 10);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Get Customers',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
            ];

            if ($result['success']) {
                $data = $result['data'];
                $response['data'] = [
                    'customers_count' => $data['customers_count'] ?? 0,
                    'total_customers' => $data['total_customers'] ?? 0,
                    'page' => $data['page'] ?? 1,
                    'limit' => $data['limit'] ?? 10,
                    'total_pages' => $data['total_pages'] ?? 0,
                    'customers' => $data['customers'] ?? [],
                    'source' => $result['source']
                ];
                $response['message'] = 'Lấy customers thành công';
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Lấy customers thất bại';
            }

            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($response, $statusCode, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'test' => 'Get Customers',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'message' => 'Lỗi exception trong quá trình test customers'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test lấy member levels
     */
    public function testMemberLevels(): JsonResponse
    {
        $startTime = microtime(true);

        try {
            $result = $this->mshopkeeperService->getMemberLevels(1, 50);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Get Member Levels',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
            ];

            if ($result['success']) {
                $data = $result['data'];
                $response['data'] = [
                    'member_levels_count' => $data['member_levels_count'] ?? 0,
                    'total_member_levels' => $data['total_member_levels'] ?? 0,
                    'page' => $data['page'] ?? 1,
                    'limit' => $data['limit'] ?? 50,
                    'total_pages' => $data['total_pages'] ?? 0,
                    'member_levels' => $data['member_levels'] ?? [],
                    'source' => $result['source']
                ];
                $response['message'] = 'Lấy member levels thành công';
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Lấy member levels thất bại';
            }

            $statusCode = $result['success'] ? 200 : 400;
            return response()->json($response, $statusCode, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'test' => 'Get Member Levels',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'message' => 'Lỗi exception trong quá trình test member levels'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test toàn bộ flow
     */
    public function fullTest(): JsonResponse
    {
        $startTime = microtime(true);
        $testResults = [];

        try {
            // Test 1: Authentication
            $authStartTime = microtime(true);
            $authResult = $this->mshopkeeperService->authenticate();
            $authTime = round((microtime(true) - $authStartTime) * 1000, 2);
            
            $testResults['authentication'] = [
                'success' => $authResult['success'],
                'execution_time_ms' => $authTime,
                'source' => $authResult['source'] ?? null,
                'error' => $authResult['success'] ? null : $authResult['error']
            ];

            // Test 2: Categories Flat (chỉ chạy nếu auth thành công)
            if ($authResult['success']) {
                $categoriesStartTime = microtime(true);
                $categoriesResult = $this->mshopkeeperService->getCategories();
                $categoriesTime = round((microtime(true) - $categoriesStartTime) * 1000, 2);

                $testResults['categories_flat'] = [
                    'success' => $categoriesResult['success'],
                    'execution_time_ms' => $categoriesTime,
                    'source' => $categoriesResult['source'] ?? null,
                    'categories_count' => $categoriesResult['success'] && isset($categoriesResult['data']['categories_count'])
                        ? $categoriesResult['data']['categories_count'] : 0,
                    'error' => $categoriesResult['success'] ? null : $categoriesResult['error']
                ];
            } else {
                $testResults['categories_flat'] = [
                    'success' => false,
                    'execution_time_ms' => 0,
                    'error' => 'Bỏ qua do authentication thất bại'
                ];
            }

            // Test 3: Categories Tree (chỉ chạy nếu auth thành công)
            if ($authResult['success']) {
                $categoriesTreeStartTime = microtime(true);
                $categoriesTreeResult = $this->mshopkeeperService->getCategoriesTree();
                $categoriesTreeTime = round((microtime(true) - $categoriesTreeStartTime) * 1000, 2);

                $testResults['categories_tree'] = [
                    'success' => $categoriesTreeResult['success'],
                    'execution_time_ms' => $categoriesTreeTime,
                    'source' => $categoriesTreeResult['source'] ?? null,
                    'categories_count' => $categoriesTreeResult['success'] && isset($categoriesTreeResult['data']['categories_count'])
                        ? $categoriesTreeResult['data']['categories_count'] : 0,
                    'tree_depth' => $categoriesTreeResult['success'] && isset($categoriesTreeResult['data']['tree_depth'])
                        ? $categoriesTreeResult['data']['tree_depth'] : 0,
                    'root_categories' => $categoriesTreeResult['success'] && isset($categoriesTreeResult['data']['categories_tree'])
                        ? count($categoriesTreeResult['data']['categories_tree']) : 0,
                    'error' => $categoriesTreeResult['success'] ? null : $categoriesTreeResult['error']
                ];
            } else {
                $testResults['categories_tree'] = [
                    'success' => false,
                    'execution_time_ms' => 0,
                    'error' => 'Bỏ qua do authentication thất bại'
                ];
            }

            // Test 4: Branchs (chỉ chạy nếu auth thành công)
            if ($authResult['success']) {
                $branchsStartTime = microtime(true);
                $branchsResult = $this->mshopkeeperService->getBranchs();
                $branchsTime = round((microtime(true) - $branchsStartTime) * 1000, 2);

                $testResults['branchs'] = [
                    'success' => $branchsResult['success'],
                    'execution_time_ms' => $branchsTime,
                    'source' => $branchsResult['source'] ?? null,
                    'branchs_count' => $branchsResult['success'] && isset($branchsResult['data']['branchs_count'])
                        ? $branchsResult['data']['branchs_count'] : 0,
                    'base_depot_count' => $branchsResult['success'] && isset($branchsResult['data']['base_depot_count'])
                        ? $branchsResult['data']['base_depot_count'] : 0,
                    'error' => $branchsResult['success'] ? null : $branchsResult['error']
                ];
            } else {
                $testResults['branchs'] = [
                    'success' => false,
                    'execution_time_ms' => 0,
                    'error' => 'Bỏ qua do authentication thất bại'
                ];
            }

            // Test 5: Customers (chỉ chạy nếu auth thành công)
            if ($authResult['success']) {
                $customersStartTime = microtime(true);
                $customersResult = $this->mshopkeeperService->getCustomers(1, 5);
                $customersTime = round((microtime(true) - $customersStartTime) * 1000, 2);

                $testResults['customers'] = [
                    'success' => $customersResult['success'],
                    'execution_time_ms' => $customersTime,
                    'source' => $customersResult['source'] ?? null,
                    'customers_count' => $customersResult['success'] && isset($customersResult['data']['customers_count'])
                        ? $customersResult['data']['customers_count'] : 0,
                    'total_customers' => $customersResult['success'] && isset($customersResult['data']['total_customers'])
                        ? $customersResult['data']['total_customers'] : 0,
                    'error' => $customersResult['success'] ? null : $customersResult['error']
                ];
            } else {
                $testResults['customers'] = [
                    'success' => false,
                    'execution_time_ms' => 0,
                    'error' => 'Bỏ qua do authentication thất bại'
                ];
            }

            // Test 6: Member Levels (chỉ chạy nếu auth thành công)
            if ($authResult['success']) {
                $memberLevelsStartTime = microtime(true);
                $memberLevelsResult = $this->mshopkeeperService->getMemberLevels(1, 10);
                $memberLevelsTime = round((microtime(true) - $memberLevelsStartTime) * 1000, 2);

                $testResults['member_levels'] = [
                    'success' => $memberLevelsResult['success'],
                    'execution_time_ms' => $memberLevelsTime,
                    'source' => $memberLevelsResult['source'] ?? null,
                    'member_levels_count' => $memberLevelsResult['success'] && isset($memberLevelsResult['data']['member_levels_count'])
                        ? $memberLevelsResult['data']['member_levels_count'] : 0,
                    'total_member_levels' => $memberLevelsResult['success'] && isset($memberLevelsResult['data']['total_member_levels'])
                        ? $memberLevelsResult['data']['total_member_levels'] : 0,
                    'error' => $memberLevelsResult['success'] ? null : $memberLevelsResult['error']
                ];
            } else {
                $testResults['member_levels'] = [
                    'success' => false,
                    'execution_time_ms' => 0,
                    'error' => 'Bỏ qua do authentication thất bại'
                ];
            }

            $totalTime = round((microtime(true) - $startTime) * 1000, 2);
            $overallSuccess = $testResults['authentication']['success'] &&
                             ($testResults['categories_flat']['success'] ?? false) &&
                             ($testResults['categories_tree']['success'] ?? false) &&
                             ($testResults['branchs']['success'] ?? false) &&
                             ($testResults['customers']['success'] ?? false) &&
                             ($testResults['member_levels']['success'] ?? false);

            return response()->json([
                'test' => 'Full Integration Test',
                'timestamp' => Carbon::now()->toISOString(),
                'total_execution_time_ms' => $totalTime,
                'overall_success' => $overallSuccess,
                'results' => $testResults,
                'summary' => [
                    'authentication' => $testResults['authentication']['success'] ? 'PASS' : 'FAIL',
                    'categories_flat' => ($testResults['categories_flat']['success'] ?? false) ? 'PASS' : 'FAIL',
                    'categories_tree' => ($testResults['categories_tree']['success'] ?? false) ? 'PASS' : 'FAIL',
                    'branchs' => ($testResults['branchs']['success'] ?? false) ? 'PASS' : 'FAIL',
                    'customers' => ($testResults['customers']['success'] ?? false) ? 'PASS' : 'FAIL',
                    'member_levels' => ($testResults['member_levels']['success'] ?? false) ? 'PASS' : 'FAIL',
                ],
                'message' => $overallSuccess ? 'Tất cả test đều thành công' : 'Một hoặc nhiều test thất bại'
            ], $overallSuccess ? 200 : 400, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            $totalTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return response()->json([
                'test' => 'Full Integration Test',
                'timestamp' => Carbon::now()->toISOString(),
                'total_execution_time_ms' => $totalTime,
                'overall_success' => false,
                'results' => $testResults,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'message' => 'Lỗi exception trong quá trình full test'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Clear cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $result = $this->mshopkeeperService->clearCache();
            
            return response()->json([
                'action' => 'Clear Cache',
                'timestamp' => Carbon::now()->toISOString(),
                'success' => $result,
                'message' => $result ? 'Cache đã được xóa thành công' : 'Không thể xóa cache'
            ], $result ? 200 : 500, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'action' => 'Clear Cache',
                'timestamp' => Carbon::now()->toISOString(),
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage()
                ],
                'message' => 'Lỗi exception khi xóa cache'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * ========================================
     * CUSTOMER APIs TEST METHODS
     * ========================================
     */

    /**
     * Test customers by info API
     */
    public function testCustomersByInfo(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $keySearch = $request->get('key_search', '0987555222'); // Default test phone

        try {
            $result = $this->mshopkeeperService->getCustomersByInfo($keySearch);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Customers By Info',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
                'key_search' => $keySearch
            ];

            if ($result['success']) {
                $data = $result['data'];
                $response['data'] = [
                    'customers_count' => $data['customers_count'],
                    'total_customers' => $data['total_customers'],
                    'customers' => $data['customers'],
                    'environment' => $data['environment'] ?? 'unknown',
                    'source' => $result['source']
                ];
                $response['message'] = "Tìm thấy {$data['customers_count']} khách hàng với từ khóa '{$keySearch}'";
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Không thể lấy danh sách khách hàng theo thông tin';
            }

            return response()->json($response, $result['success'] ? 200 : 400, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'test' => 'Customers By Info',
                'timestamp' => Carbon::now()->toISOString(),
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage()
                ],
                'message' => 'Lỗi exception khi test customers by info'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test Lomas customer search API
     */
    public function testLomasCustomerSearch(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $keyword = $request->get('keyword', '0326643186'); // Default test phone

        try {
            $result = $this->mshopkeeperService->searchLomasCustomerInfo($keyword);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Lomas Customer Search',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
                'keyword' => $keyword
            ];

            if ($result['success']) {
                $data = $result['data'];
                $response['data'] = [
                    'customer' => $data['customer'],
                    'card_policies' => $data['card_policies'],
                    'card_policies_count' => count($data['card_policies']),
                    'total' => $data['total'],
                    'environment' => $data['environment'] ?? 'unknown',
                    'source' => $result['source']
                ];

                if ($data['customer']) {
                    $customer = $data['customer'];
                    $response['message'] = "Tìm thấy khách hàng: {$customer['FullName']} - {$customer['Tel']} - Hạng thẻ: {$customer['CardName']}";
                } else {
                    $response['message'] = "Không tìm thấy khách hàng với từ khóa '{$keyword}'";
                }
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Không thể tìm kiếm thông tin khách hàng Lomas';
            }

            return response()->json($response, $result['success'] ? 200 : 400, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'test' => 'Lomas Customer Search',
                'timestamp' => Carbon::now()->toISOString(),
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage()
                ],
                'message' => 'Lỗi exception khi test Lomas customer search'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Test customers point paging API
     */
    public function testCustomersPointPaging(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        $page = (int) $request->get('page', 1);
        $limit = (int) $request->get('limit', 10);

        try {
            $result = $this->mshopkeeperService->getCustomersPointPaging($page, $limit);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $response = [
                'test' => 'Customers Point Paging',
                'timestamp' => Carbon::now()->toISOString(),
                'execution_time_ms' => $executionTime,
                'success' => $result['success'],
                'page' => $page,
                'limit' => $limit
            ];

            if ($result['success']) {
                $data = $result['data'];
                $response['data'] = [
                    'customer_points_count' => $data['customer_points_count'],
                    'total_customer_points' => $data['total_customer_points'],
                    'total_pages' => $data['total_pages'],
                    'customer_points' => $data['customer_points'],
                    'environment' => $data['environment'] ?? 'unknown',
                    'source' => $result['source']
                ];
                $response['message'] = "Lấy được {$data['customer_points_count']} khách hàng có điểm (trang {$page}/{$data['total_pages']})";
            } else {
                $response['error'] = $result['error'];
                $response['message'] = 'Không thể lấy danh sách điểm khách hàng';
            }

            return response()->json($response, $result['success'] ? 200 : 400, [], JSON_PRETTY_PRINT);

        } catch (\Exception $e) {
            return response()->json([
                'test' => 'Customers Point Paging',
                'timestamp' => Carbon::now()->toISOString(),
                'success' => false,
                'error' => [
                    'type' => 'EXCEPTION',
                    'message' => $e->getMessage()
                ],
                'message' => 'Lỗi exception khi test customers point paging'
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }
}
