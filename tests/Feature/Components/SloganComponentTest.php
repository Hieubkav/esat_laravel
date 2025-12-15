<?php

namespace Tests\Feature\Components;

use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;

class SloganComponentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache trước mỗi test
        Cache::flush();
    }

    /** @test */
    public function it_displays_slogan_when_data_exists()
    {
        // Tạo setting với slogan
        Setting::create([
            'site_name' => 'Vũ Phúc Baking',
            'slogan' => 'Bánh ngọt tươi ngon mỗi ngày',
            'footer_description' => 'Chúng tôi cam kết mang đến những sản phẩm bánh ngọt chất lượng cao.',
            'status' => 'active',
            'order' => 1
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bánh ngọt tươi ngon mỗi ngày');
        $response->assertSee('Chúng tôi cam kết mang đến những sản phẩm bánh ngọt chất lượng cao.');
    }

    /** @test */
    public function it_hides_component_when_no_slogan()
    {
        // Tạo setting không có slogan
        Setting::create([
            'site_name' => 'Vũ Phúc Baking',
            'slogan' => '',
            'status' => 'active',
            'order' => 1
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('slogan-heading');
    }

    /** @test */
    public function it_hides_component_when_slogan_is_null()
    {
        // Tạo setting với slogan null
        Setting::create([
            'site_name' => 'Vũ Phúc Baking',
            'slogan' => null,
            'status' => 'active',
            'order' => 1
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('slogan-heading');
    }

    /** @test */
    public function it_hides_component_when_slogan_is_whitespace_only()
    {
        // Tạo setting với slogan chỉ có khoảng trắng
        Setting::create([
            'site_name' => 'Vũ Phúc Baking',
            'slogan' => '   ',
            'status' => 'active',
            'order' => 1
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('slogan-heading');
    }

    /** @test */
    public function it_displays_slogan_without_description_when_description_is_empty()
    {
        // Tạo setting với slogan nhưng không có description
        Setting::create([
            'site_name' => 'Vũ Phúc Baking',
            'slogan' => 'Bánh ngọt tươi ngon mỗi ngày',
            'footer_description' => '',
            'status' => 'active',
            'order' => 1
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bánh ngọt tươi ngon mỗi ngày');
        $response->assertSee('slogan-heading');
        // Kiểm tra không có description wrapper
        $response->assertDontSee('max-w-2xl lg:max-w-3xl xl:max-w-4xl mx-auto');
    }

    /** @test */
    public function it_hides_component_when_no_settings_exist()
    {
        // Không tạo setting nào

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('slogan-heading');
    }

    /** @test */
    public function it_hides_component_when_setting_is_inactive()
    {
        // Tạo setting với status inactive
        Setting::create([
            'site_name' => 'Vũ Phúc Baking',
            'slogan' => 'Bánh ngọt tươi ngon mỗi ngày',
            'status' => 'inactive',
            'order' => 1
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Bánh ngọt tươi ngon mỗi ngày');
        $response->assertDontSee('slogan-heading');
    }

    /** @test */
    public function it_trims_whitespace_from_slogan_and_description()
    {
        // Tạo setting với slogan và description có khoảng trắng
        Setting::create([
            'site_name' => 'Vũ Phúc Baking',
            'slogan' => '  Bánh ngọt tươi ngon mỗi ngày  ',
            'footer_description' => '  Chúng tôi cam kết chất lượng  ',
            'status' => 'active',
            'order' => 1
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bánh ngọt tươi ngon mỗi ngày');
        $response->assertSee('Chúng tôi cam kết chất lượng');
        // Kiểm tra không có khoảng trắng thừa
        $response->assertDontSee('  Bánh ngọt tươi ngon mỗi ngày  ');
    }
}
