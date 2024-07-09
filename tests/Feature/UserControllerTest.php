<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create an admin user for authentication
        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    /** @test */
    public function it_can_login()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['token']]);
    }

    /** @test */
    public function it_can_logout()
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_get_authenticated_user()
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/v1/user');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function it_can_get_user_orders()
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/v1/user/orders');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data']);
    }

    /** @test */
    public function it_can_create_user()
    {
        $response = $this->postJson('/api/v1/user/create', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => '123 Main St',
            'phone_number' => '1234567890',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['token']]);
    }

    /** @test */
    public function it_can_edit_user()
    {
        $token = $this->user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson('/api/v1/user/edit', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'user@example.com',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
                'address' => '456 Main St',
                'phone_number' => '0987654321',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_handle_forgot_password()
    {
        $response = $this->postJson('/api/v1/user/forgot-password', [
            'email' => 'user@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data' => ['reset_token']]);
    }

    /** @test */
    public function it_can_handle_reset_password()
    {
        $token = Password::createToken($this->user);

        $response = $this->postJson('/api/v1/user/reset-password-token', [
            'email' => 'user@example.com',
            'token' => $token,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
