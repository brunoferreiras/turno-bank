<?php

namespace Tests\Feature;

use App\Enums\UserTypes;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_should_create_a_new_user_as_customer(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->username,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $this->assertDatabaseMissing('users', [
            'username' => $payload['username'],
        ]);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('accounts', 0);
        $response = $this->postJson('/api/auth/register', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'user',
            ]);
        $this->assertEquals(UserTypes::CUSTOMER->value, $response['user']['type']);
        $this->assertDatabaseHas('accounts', [
            'user_id' => $response['user']['id'],
        ]);
    }

    /** @test */
    public function it_should_return_error_during_create_a_new_user(): void
    {
        $payload = User::factory()->unverified()->make()->toArray();
        $payload['password'] = 'password123';
        $this->mock(UserRepository::class, function ($mock) {
            $mock->shouldReceive('register')
                ->once()
                ->andThrow(new Exception('Any error'));
        });
        $response = $this->postJson('/api/auth/register', $payload);
        $response->assertStatus(500)
            ->assertJson([
                'message' => 'Error during create a new user',
            ]);
    }

    /** @test */
    public function it_should_return_invalid_credentials(): void
    {
        $payload = [
            'username' => $this->faker->userName(),
            'password' => 'password123',
        ];
        $response = $this->postJson('/api/auth/login', $payload);
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized',
            ]);
    }

    /** @test */
    public function it_should_login_an_user_as_customer(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::CUSTOMER->value,
            'password' => bcrypt('password'),
        ]);
        $payload = [
            'username' => $user->username,
            'password' => 'password',
        ];
        $response = $this->postJson('/api/auth/login', $payload);
        $response->assertOk()
            ->assertJsonStructure([
                'user',
                'authorization'
            ]);
        $this->assertEquals(UserTypes::CUSTOMER->value, $response['user']['type']);
    }

    /** @test */
    public function it_should_login_an_user_as_admin(): void
    {
        $user = User::factory()->create([
            'type' => UserTypes::ADMIN->value,
            'password' => bcrypt('password'),
        ]);
        $payload = [
            'username' => $user->username,
            'password' => 'password',
        ];
        $response = $this->postJson('/api/auth/login', $payload);
        $response->assertOk()
            ->assertJsonStructure([
                'user',
                'authorization'
            ]);
        $this->assertEquals(UserTypes::ADMIN->value, $response['user']['type']);
    }

    /** @test */
    public function it_should_logout_a_user(): void
    {
        $user = User::factory()->create();
        $token = Auth::login($user);
        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(204);
    }

    /** @test */
    public function it_should_refresh_a_token(): void
    {
        $user = User::factory()->create();
        $token = Auth::login($user);
        $response = $this->postJson('/api/auth/refresh', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertOk()
            ->assertJsonStructure([
                'user',
                'authorization'
            ]);
    }
}
