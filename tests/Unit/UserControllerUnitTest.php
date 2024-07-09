<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\UserController;
use App\Models\JWTToken;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Mockery;
use Tests\TestCase;

class UserControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $jwtService;
    protected $userController;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtService = Mockery::mock(JwtService::class);
        $this->userController = new UserController($this->jwtService);

        $this->user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        Auth::shouldReceive('attempt')->andReturn(true);
        $this->jwtService->shouldReceive('issueToken')->andReturn('fake-jwt-token');
    }

    /** @test */
    public function it_can_login()
    {
        $request = Request::create('/api/v1/login', 'POST', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response = $this->userController->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $response->getOriginalContent()['data']);
    }

    /** @test */
    public function it_can_logout()
    {
        $request = Request::create('/api/v1/logout', 'GET', [], [
            'jwt' => 'fake-jwt-token',
        ]);

        $this->jwtService->config = Mockery::mock();
        $this->jwtService->config->shouldReceive('parser->parse')->andReturn((object)['claims' => (object)['get' => 'fake-jti']]);

        $response = $this->userController->logout($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Logged out', $response->getOriginalContent()['message']);
    }

    /** @test */
    public function it_can_get_authenticated_user()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->andReturn($this->user);

        $response = $this->userController->user($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $response->getOriginalContent());
    }

    /** @test */
    public function it_can_get_user_orders()
    {
        $request = Request::create('/api/v1/user/orders', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ]);

        $request->setUserResolver(function () {
            return $this->user;
        });

        $response = $this->userController->getOrders($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('data', $response->getOriginalContent());
    }

    /** @test */
    public function it_can_create_user()
    {
        $request = Request::create('/api/v1/user/create', 'POST', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'address' => '123 Main St',
            'phone_number' => '1234567890',
        ]);

        $response = $this->userController->createUser($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
        $this->assertArrayHasKey('token', $response->getOriginalContent()['data']);
    }

    /** @test */
    public function it_can_edit_user()
    {
        $request = Request::create('/api/v1/user/edit', 'PUT', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'user@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'address' => '456 Main St',
            'phone_number' => '0987654321',
        ]);

        $request->setUserResolver(function () {
            return $this->user;
        });

        $response = $this->userController->editUser($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', ['email' => 'user@example.com', 'first_name' => 'John', 'address' => '456 Main St']);
    }

    /** @test */
    public function it_can_handle_forgot_password()
    {
        Password::shouldReceive('createToken')->andReturn('fake-reset-token');

        $request = Request::create('/api/v1/user/forgot-password', 'POST', [
            'email' => 'user@example.com',
        ]);

        $response = $this->userController->forgotPassword($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('reset_token', $response->getOriginalContent()['data']);
    }

    /** @test */
    public function it_can_handle_reset_password()
    {
        Password::shouldReceive('reset')->andReturn(Password::PASSWORD_RESET);

        $request = Request::create('/api/v1/user/reset-password-token', 'POST', [
            'email' => 'user@example.com',
            'token' => 'fake-reset-token',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response = $this->userController->resetPassword($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Password reset successfully', $response->getOriginalContent()['message']);
    }
}
