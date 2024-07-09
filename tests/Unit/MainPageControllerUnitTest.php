<?php

namespace Tests\Unit;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\V1\MainPageController;
use App\Models\Post;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class MainPageControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $mainPageController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mainPageController = new MainPageController();
    }

    /** @test */
    public function it_can_get_blog_posts()
    {
        Post::factory()->count(5)->create();

        $request = Request::create('/api/v1/main/blog', 'GET', [
            'limit' => 10,
            'sort' => 'desc',
            'sort_by' => 'created_at'
        ]);

        $response = $this->mainPageController->getBlogPosts($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $response->getOriginalContent());
    }

    /** @test */
    public function it_can_get_promotions()
    {
        Promotion::factory()->count(5)->create();

        $request = Request::create('/api/v1/main/promotions', 'GET', [
            'limit' => 10,
            'sort' => 'desc',
            'sort_by' => 'created_at',
            'page' => 1
        ]);

        $response = $this->mainPageController->getPromotions($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $response->getOriginalContent());
    }

    /** @test */
    public function it_can_get_a_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->mainPageController->getPost($post->uuid);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $response->getOriginalContent());
    }

    /** @test */
    public function it_returns_404_when_post_not_found()
    {
        $response = $this->mainPageController->getPost('non-existing-uuid');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Post not found', $response->getOriginalContent()['message']);
    }
}
