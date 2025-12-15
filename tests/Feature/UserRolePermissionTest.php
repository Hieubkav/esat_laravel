<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo users test
        $this->adminUser = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        $this->postManagerUser = User::factory()->create([
            'email' => 'postmanager@test.com',
            'role' => 'post_manager',
        ]);
    }

    public function test_admin_can_access_all_resources()
    {
        $this->assertTrue($this->adminUser->isAdmin());
        $this->assertFalse($this->adminUser->isPostManager());
        $this->assertTrue($this->adminUser->canAccessResource('PostResource'));
        $this->assertTrue($this->adminUser->canAccessResource('ProductResource'));
        $this->assertTrue($this->adminUser->canAccessResource('UserResource'));
    }

    public function test_post_manager_can_only_access_post_resources()
    {
        $this->assertFalse($this->postManagerUser->isAdmin());
        $this->assertTrue($this->postManagerUser->isPostManager());
        $this->assertTrue($this->postManagerUser->canAccessResource('PostResource'));
        $this->assertTrue($this->postManagerUser->canAccessResource('PostCategoryResource'));
        $this->assertFalse($this->postManagerUser->canAccessResource('ProductResource'));
        $this->assertFalse($this->postManagerUser->canAccessResource('UserResource'));
    }

    public function test_admin_can_access_admin_panel()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function test_post_manager_can_access_admin_panel()
    {
        $response = $this->actingAs($this->postManagerUser)
            ->get('/admin');

        $response->assertStatus(200);
    }

    public function test_post_manager_can_access_posts()
    {
        $response = $this->actingAs($this->postManagerUser)
            ->get('/admin/posts');

        $response->assertStatus(200);
    }

    public function test_post_manager_can_access_post_categories()
    {
        $response = $this->actingAs($this->postManagerUser)
            ->get('/admin/post-categories');

        $response->assertStatus(200);
    }

    public function test_post_manager_cannot_access_products()
    {
        $response = $this->actingAs($this->postManagerUser)
            ->get('/admin/products');

        $response->assertStatus(403);
    }

    public function test_post_manager_cannot_access_users()
    {
        $response = $this->actingAs($this->postManagerUser)
            ->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }
}
