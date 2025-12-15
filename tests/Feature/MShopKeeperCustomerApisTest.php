<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\MShopKeeperService;
use Illuminate\Support\Facades\Cache;

class MShopKeeperCustomerApisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::flush();

        // Set mock mode for testing
        config(['mshopkeeper.mock_mode' => true]);
    }

    private function getService(): MShopKeeperService
    {
        return new MShopKeeperService();
    }

    /**
     * Test customers by info API - Success case
     */
    public function test_get_customers_by_info_success(): void
    {
        $keySearch = '0987555222';

        $result = $this->getService()->getCustomersByInfo($keySearch);

        $this->assertTrue($result['success']);
        $this->assertEquals('mock', $result['source']);
        $this->assertArrayHasKey('data', $result);

        $data = $result['data'];
        $this->assertArrayHasKey('customers', $data);
        $this->assertArrayHasKey('customers_count', $data);
        $this->assertArrayHasKey('total_customers', $data);
        $this->assertArrayHasKey('key_search', $data);
        $this->assertEquals($keySearch, $data['key_search']);

        // Verify customer structure
        if (!empty($data['customers'])) {
            $customer = $data['customers'][0];
            $this->assertArrayHasKey('CustomerID', $customer);
            $this->assertArrayHasKey('CustomerCode', $customer);
            $this->assertArrayHasKey('CustomerName', $customer);
            $this->assertArrayHasKey('Tel', $customer);
            $this->assertArrayHasKey('Email', $customer);
        }
    }

    /**
     * Test customers by info API - Empty search
     */
    public function test_get_customers_by_info_empty_search(): void
    {
        $keySearch = 'nonexistent@email.com';

        $result = $this->getService()->getCustomersByInfo($keySearch);

        $this->assertTrue($result['success']);
        $this->assertEquals('mock', $result['source']);

        $data = $result['data'];
        $this->assertEquals(0, $data['customers_count']);
        $this->assertEmpty($data['customers']);
    }

    /**
     * Test Lomas customer search API - Success case
     */
    public function test_search_lomas_customer_info_success(): void
    {
        $keyword = '0326643186';

        $result = $this->getService()->searchLomasCustomerInfo($keyword);

        $this->assertTrue($result['success']);
        $this->assertEquals('mock', $result['source']);
        $this->assertArrayHasKey('data', $result);

        $data = $result['data'];
        $this->assertArrayHasKey('customer', $data);
        $this->assertArrayHasKey('card_policies', $data);
        $this->assertArrayHasKey('keyword', $data);
        $this->assertEquals($keyword, $data['keyword']);

        // Verify customer found
        $this->assertNotNull($data['customer']);
        $customer = $data['customer'];
        $this->assertArrayHasKey('Id', $customer);
        $this->assertArrayHasKey('Code', $customer);
        $this->assertArrayHasKey('FullName', $customer);
        $this->assertArrayHasKey('Tel', $customer);
        $this->assertArrayHasKey('CardName', $customer);
        $this->assertArrayHasKey('TotalPoint', $customer);

        // Verify card policies
        $this->assertIsArray($data['card_policies']);
        if (!empty($data['card_policies'])) {
            $policy = $data['card_policies'][0];
            $this->assertArrayHasKey('Name', $policy);
            $this->assertArrayHasKey('Type', $policy);
            $this->assertArrayHasKey('Amount', $policy);
            $this->assertArrayHasKey('Point', $policy);
        }
    }

    /**
     * Test Lomas customer search API - Not found
     */
    public function test_search_lomas_customer_info_not_found(): void
    {
        $keyword = 'nonexistent';

        $result = $this->getService()->searchLomasCustomerInfo($keyword);

        $this->assertTrue($result['success']);
        $this->assertEquals('mock', $result['source']);

        $data = $result['data'];
        $this->assertNull($data['customer']);
        $this->assertEmpty($data['card_policies']);
        $this->assertEquals(0, $data['total']);
    }

    /**
     * Test customers point paging API - Success case
     */
    public function test_get_customers_point_paging_success(): void
    {
        $page = 1;
        $limit = 5;
        
        $result = $this->getService()->getCustomersPointPaging($page, $limit);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('mock', $result['source']);
        $this->assertArrayHasKey('data', $result);
        
        $data = $result['data'];
        $this->assertArrayHasKey('customer_points', $data);
        $this->assertArrayHasKey('customer_points_count', $data);
        $this->assertArrayHasKey('total_customer_points', $data);
        $this->assertArrayHasKey('page', $data);
        $this->assertArrayHasKey('limit', $data);
        $this->assertArrayHasKey('total_pages', $data);
        
        $this->assertEquals($page, $data['page']);
        $this->assertEquals($limit, $data['limit']);
        $this->assertLessThanOrEqual($limit, $data['customer_points_count']);
        
        // Verify customer point structure
        if (!empty($data['customer_points'])) {
            $customerPoint = $data['customer_points'][0];
            $this->assertArrayHasKey('Tel', $customerPoint);
            $this->assertArrayHasKey('TotalPoint', $customerPoint);
            $this->assertArrayHasKey('OriginalId', $customerPoint);
            $this->assertArrayHasKey('FullName', $customerPoint);
        }
    }

    /**
     * Test customers point paging API - Pagination
     */
    public function test_get_customers_point_paging_pagination(): void
    {
        $page = 2;
        $limit = 3;
        
        $result = $this->getService()->getCustomersPointPaging($page, $limit);
        
        $this->assertTrue($result['success']);
        
        $data = $result['data'];
        $this->assertEquals($page, $data['page']);
        $this->assertEquals($limit, $data['limit']);
        
        // Verify pagination calculation
        $expectedTotalPages = ceil($data['total_customer_points'] / $limit);
        $this->assertEquals($expectedTotalPages, $data['total_pages']);
    }

    /**
     * Test customers point paging API - Limit validation
     */
    public function test_get_customers_point_paging_limit_validation(): void
    {
        $page = 1;
        $limit = 150; // Over max limit of 100
        
        $result = $this->getService()->getCustomersPointPaging($page, $limit);
        
        $this->assertTrue($result['success']);
        
        $data = $result['data'];
        // Should be limited to available data, not necessarily 100
        $this->assertLessThanOrEqual(100, $data['customer_points_count']);
    }

    /**
     * Test cache functionality for customers by info
     */
    public function test_customers_by_info_cache(): void
    {
        $keySearch = '0987555222';
        
        // First call - should be from mock
        $result1 = $this->getService()->getCustomersByInfo($keySearch);
        $this->assertEquals('mock', $result1['source']);

        // Second call - should be from cache (but in mock mode, always returns mock)
        $result2 = $this->getService()->getCustomersByInfo($keySearch);
        $this->assertEquals('mock', $result2['source']);
        
        // Results should be identical
        $this->assertEquals($result1['data'], $result2['data']);
    }

    /**
     * Test error handling with invalid parameters
     */
    public function test_customers_by_info_empty_key_search(): void
    {
        $keySearch = '';
        
        $result = $this->getService()->getCustomersByInfo($keySearch);
        
        // Mock should still work with empty search
        $this->assertTrue($result['success']);
        $this->assertEquals(0, $result['data']['customers_count']);
    }

    /**
     * Test Lomas customer search with card code
     */
    public function test_search_lomas_customer_info_with_card_code(): void
    {
        $keyword = '100000131'; // Card code instead of phone
        
        $result = $this->getService()->searchLomasCustomerInfo($keyword);
        
        $this->assertTrue($result['success']);
        
        $data = $result['data'];
        $this->assertNotNull($data['customer']);
        $this->assertEquals('100000131', $data['customer']['Code']);
    }
}
