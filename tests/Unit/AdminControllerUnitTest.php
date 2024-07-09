<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\AdminController;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class AdminControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $jwtService;
    protected $adminController;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwtService = Mockery::mock(JwtService::class);
        $this->adminController = new AdminController($this->jwtService);

        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        Auth::shouldReceive('attempt')->andReturn(true);
        $this->jwtService->shouldReceive('issueToken')->andReturn('fake-jwt-token');
    }

    /** @test */
    public function it_can_login()
    {
        $request = Request::create('/api/v1/admin/login', 'POST', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response = $this->adminController->login($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $response->getOriginalContent()['data']);
    }

    /** @test */
    public function it_can_create_admin_user()
    {
        $request = Request::create('/api/v1/admin/create', 'POST', [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'address' => '123 Admin St',
            'phone_number' => '1234567890',
            'avatar' => 'http://example.com/avatar.png',
            'email' => 'newadmin@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->adminController->createAdminUser($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', ['email' => 'newadmin@example.com']);
    }

    /** @test */
    public function it_can_delete_user()
    {
        $user = User::factory()->create();

        $request = Request::create('/api/v1/admin/user-delete/' . $user->uuid, 'DELETE');

        $response = $this->adminController->deleteUser($request, $user->uuid);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSoftDeleted('users', ['uuid' => $user->uuid]);
    }

    /** @test */
    public function it_can_edit_user()
    {
        $user = User::factory()->create();

        $request = Request::create('/api/v1/admin/user-edit/' . $user->uuid, 'PUT', [
            'first_name' => 'Updated',
            'last_name' => 'Name',
        ]);

        $response = $this->adminController->editUser($request, $user->uuid);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('users', ['uuid' => $user->uuid, 'first_name' => 'Updated', 'last_name' => 'Name']);
    }
}
