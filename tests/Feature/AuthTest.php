<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_unauthenticated()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }

    public function test_get_user_authenticated()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/auth/user');

        $response->assertStatus(200);
    }

    public function test_user_logged_in_success()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'unit_test',
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'token',
                'user',
            ]
        ]);
        $response->assertJsonPath('data.user.name', $user->name);
        $response->assertJsonPath('data.user.email', $user->email);
    }

    public function test_user_logged_in_failed()
    {
        $user = User::factory()->create();
        $data = [
            'email' => $user->email,
            'password' => 'invalid_password',
            'device_name' => 'unit_test',
        ];

        $response = $this->postJson('/api/auth/login', $data);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => [
                'code' => 401,
                'message' => 'The provided credentials are incorrect.',
            ]
        ]);
    }

    public function test_user_logged_out()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/auth/logout');

        $response->assertStatus(204);
        $this->assertCount(0, $user->tokens);
    }
}
