<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Perform login to get the token
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->token = $response->json('data.token');
        echo 'TOKenenennenenenenenne:   ' . $this->token;
    }

    /** @test */
    public function it_can_login_as_admin()
    {
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['token']]);
    }

    /** @test */
    public function it_can_logout_as_admin()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/v1/admin/logout');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_admin_user()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson('/api/v1/admin/create', [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'address' => '123 Admin St',
                'phone_number' => '1234567890',
                'avatar' => 'http://example.com/avatar.png',
                'email' => 'newadmin@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['id', 'email', 'is_admin']]);
    }

    /** @test */
    public function it_can_list_users()
    {
        User::factory()->count(5)->create();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->getJson('/api/v1/admin/user-listing');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['data', 'current_page', 'last_page', 'per_page', 'total']]);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->withToken($this->token)
            ->deleteJson('/api/v1/admin/user-delete/' . $user->uuid);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_edit_user()
    {
        $user = User::factory()->create();

        $response = $this->withToken($this->token)
            ->putJson('/api/v1/admin/user-edit/' . $user->uuid, [
                'first_name' => 'Updated',
                'last_name' => 'Name',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
