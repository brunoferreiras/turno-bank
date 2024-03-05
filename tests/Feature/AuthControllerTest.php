<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_registers_a_user(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'username' => $this->faker->username,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        $response = $this->postJson('/api/auth/register', $payload);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'user',
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
    public function it_logs_in_a_user(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $payload = [
            'username' => $user->username,
            'password' => 'password',
        ];
        $response = $this->postJson('/api/auth/login', $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'authorization',
            ]);
    }

    /** @test */
    public function it_logs_out_a_user(): void
    {
        $user = User::factory()->create();
        $token = Auth::login($user);
        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(204);
    }
}
