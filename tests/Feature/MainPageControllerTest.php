<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainPageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up any common data or configurations needed for tests
    }

    /** @test */
    public function it_can_get_blog_posts()
    {
        Post::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/main/blog');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'uuid', 'title', 'content', 'created_at', 'updated_at']
                    ],
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function it_can_get_promotions()
    {
        Promotion::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/main/promotions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'description', 'discount', 'created_at', 'updated_at']
                    ],
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function it_can_get_a_single_post()
    {
        $post = Post::factory()->create();

        $response = $this->getJson('/api/v1/main/blog/' . $post->uuid);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'uuid', 'title', 'content', 'created_at', 'updated_at']
            ]);
    }

    /** @test */
    public function it_returns_404_when_post_not_found()
    {
        $response = $this->getJson('/api/v1/main/blog/non-existing-uuid');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Post not found'
            ]);
    }
}
