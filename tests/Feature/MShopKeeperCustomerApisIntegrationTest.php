<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\MShopKeeperService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MShopKeeperCustomerApisIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::flush();
    }

    private function getService(): MShopKeeperService
    {
        return new MShopKeeperService();
    }

    /**
     * Test customers by info API with real API simulation
     */
    public function test_get_customers_by_info_real_api_simulation(): void
    {
        // Disable mock mode to test real API flow
        config(['mshopkeeper.mock_mode' => false]);
        
        // Mock HTTP responses
        Http::fake([
            '*/auth/api/Account/Login' => Http::response([
                'Success' => true,
                'Data' => [
                    'AccessToken' => 'mock_access_token_12345',
                    'CompanyCode' => 'demoquanao',
                    'Environment' => 'g1'
                ]
            ], 200),
            
            '*/g1/api/v1/customers/customerbyinfo' => Http::response([
                'Code' => 200,
                'Success' => true,
                'Data' => [
                    [
                        'CustomerID' => '99bb83d7-a4c5-4d64-a152-b30d89433838',
                        'CustomerCode' => 'KH000012',
                        'CustomerName' => 'Văn Bình',
                        'Tel' => '0985858583',
                        'NormalizedTel' => '84985858583',
                        'StandardTel' => '0985858583',
                        'Address' => '',
                        'Email' => '',
                        'Gender' => 0,
                        'CardName' => '',
                        'Point' => 0
                    ]
                ],
                'Total' => 1,
                'Environment' => 'g1'
            ], 200)
        ]);
        
        $keySearch = '0985858583';
        $result = $this->service->getCustomersByInfo($keySearch);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('api', $result['source']);
        $this->assertEquals(1, $result['data']['customers_count']);
        $this->assertEquals('Văn Bình', $result['data']['customers'][0]['CustomerName']);
    }

    /**
     * Test Lomas customer search API with real API simulation
     */
    public function test_search_lomas_customer_info_real_api_simulation(): void
    {
        // Disable mock mode to test real API flow
        config(['mshopkeeper.mock_mode' => false]);
        
        // Mock HTTP responses
        Http::fake([
            '*/auth/api/Account/Login' => Http::response([
                'Success' => true,
                'Data' => [
                    'AccessToken' => 'mock_access_token_12345',
                    'CompanyCode' => 'demoquanao',
                    'Environment' => 'g1'
                ]
            ], 200),
            
            '*/g1/api/v1/customers/search-lomas-info' => Http::response([
                'Code' => 200,
                'Success' => true,
                'Data' => [
                    'Customer' => [
                        'Id' => 132,
                        'Code' => '100000131',
                        'FullName' => 'Phụng Nghi 2344',
                        'Tel' => '0326643186',
                        'NormalizedTel' => '84326643186',
                        'Email' => '',
                        'Gender' => 1,
                        'CardName' => 'Kim cương',
                        'TotalPoint' => 638,
                        'TotalAmount' => 167743158.0
                    ],
                    'CardPolicies' => [
                        [
                            'Name' => 'Chính sách tích điểm',
                            'Type' => 1,
                            'Amount' => 100000.0,
                            'Point' => 1.0,
                            'Rate' => 2.0
                        ]
                    ]
                ],
                'Total' => 1,
                'Environment' => 'g1'
            ], 200)
        ]);
        
        $keyword = '0326643186';
        $result = $this->service->searchLomasCustomerInfo($keyword);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('api', $result['source']);
        $this->assertNotNull($result['data']['customer']);
        $this->assertEquals('Phụng Nghi 2344', $result['data']['customer']['FullName']);
        $this->assertCount(1, $result['data']['card_policies']);
    }

    /**
     * Test customers point paging API with real API simulation
     */
    public function test_get_customers_point_paging_real_api_simulation(): void
    {
        // Disable mock mode to test real API flow
        config(['mshopkeeper.mock_mode' => false]);
        
        // Mock HTTP responses
        Http::fake([
            '*/auth/api/Account/Login' => Http::response([
                'Success' => true,
                'Data' => [
                    'AccessToken' => 'mock_access_token_12345',
                    'CompanyCode' => 'demoquanao',
                    'Environment' => 'g1'
                ]
            ], 200),
            
            '*/g1/api/v1/customers/point-paging' => Http::response([
                'Code' => 200,
                'Success' => true,
                'Data' => [
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
                    ]
                ],
                'Total' => 575,
                'Environment' => 'g1'
            ], 200)
        ]);
        
        $page = 1;
        $limit = 10;
        $result = $this->service->getCustomersPointPaging($page, $limit);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('api', $result['source']);
        $this->assertEquals(2, $result['data']['customer_points_count']);
        $this->assertEquals(575, $result['data']['total_customer_points']);
    }

    /**
     * Test error handling - 401 Unauthorized
     */
    public function test_customers_by_info_unauthorized_error(): void
    {
        config(['mshopkeeper.mock_mode' => false]);
        
        Http::fake([
            '*/auth/api/Account/Login' => Http::response([
                'Success' => true,
                'Data' => [
                    'AccessToken' => 'invalid_token',
                    'CompanyCode' => 'demoquanao',
                    'Environment' => 'g1'
                ]
            ], 200),
            
            '*/g1/api/v1/customers/customerbyinfo' => Http::response([
                'error' => 'Unauthorized'
            ], 401)
        ]);
        
        $result = $this->service->getCustomersByInfo('test');
        
        $this->assertFalse($result['success']);
        $this->assertEquals('UNAUTHORIZED', $result['error']['type']);
        $this->assertStringContainsString('AccessToken hết hạn', $result['error']['message']);
    }

    /**
     * Test error handling - API Error
     */
    public function test_lomas_customer_search_api_error(): void
    {
        config(['mshopkeeper.mock_mode' => false]);
        
        Http::fake([
            '*/auth/api/Account/Login' => Http::response([
                'Success' => true,
                'Data' => [
                    'AccessToken' => 'valid_token',
                    'CompanyCode' => 'demoquanao',
                    'Environment' => 'g1'
                ]
            ], 200),
            
            '*/g1/api/v1/customers/search-lomas-info' => Http::response([
                'Code' => 200,
                'Success' => false,
                'ErrorType' => 1,
                'ErrorMessage' => 'Tham số không hợp lệ null or empty'
            ], 200)
        ]);
        
        $result = $this->service->searchLomasCustomerInfo('');
        
        $this->assertFalse($result['success']);
        $this->assertEquals(1, $result['error']['type']);
        $this->assertStringContainsString('Tham số không hợp lệ', $result['error']['message']);
    }

    /**
     * Test error handling - Network Exception
     */
    public function test_customers_point_paging_network_exception(): void
    {
        config(['mshopkeeper.mock_mode' => false]);
        
        Http::fake([
            '*/auth/api/Account/Login' => Http::response([
                'Success' => true,
                'Data' => [
                    'AccessToken' => 'valid_token',
                    'CompanyCode' => 'demoquanao',
                    'Environment' => 'g1'
                ]
            ], 200),
            
            '*/g1/api/v1/customers/point-paging' => Http::response([], 500)
        ]);
        
        $result = $this->service->getCustomersPointPaging(1, 10);
        
        $this->assertFalse($result['success']);
        $this->assertEquals('API_ERROR', $result['error']['type']);
    }

    /**
     * Test cache functionality in real API mode
     */
    public function test_customers_by_info_cache_in_real_mode(): void
    {
        config(['mshopkeeper.mock_mode' => false]);
        
        Http::fake([
            '*/auth/api/Account/Login' => Http::response([
                'Success' => true,
                'Data' => [
                    'AccessToken' => 'valid_token',
                    'CompanyCode' => 'demoquanao',
                    'Environment' => 'g1'
                ]
            ], 200),
            
            '*/g1/api/v1/customers/customerbyinfo' => Http::response([
                'Code' => 200,
                'Success' => true,
                'Data' => [
                    [
                        'CustomerID' => '99bb83d7-a4c5-4d64-a152-b30d89433838',
                        'CustomerCode' => 'KH000012',
                        'CustomerName' => 'Văn Bình',
                        'Tel' => '0985858583'
                    ]
                ],
                'Total' => 1,
                'Environment' => 'g1'
            ], 200)
        ]);
        
        $keySearch = '0985858583';
        
        // First call - should hit API
        $result1 = $this->service->getCustomersByInfo($keySearch);
        $this->assertEquals('api', $result1['source']);
        
        // Second call - should hit cache
        $result2 = $this->service->getCustomersByInfo($keySearch);
        $this->assertEquals('cache', $result2['source']);
        
        // Data should be identical
        $this->assertEquals($result1['data'], $result2['data']);
    }
}
