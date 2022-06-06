<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_get_role_list()
    {
        $roles = Role::factory()->count(5)->create();
        $response = $this->get('/api/roles');

        $response->assertOk();
        $response->assertJsonCount($roles->count(), 'data');
    }

    public function test_create_role()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'Admin',
            'code' => 'admin',
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/roles', $data);

        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);
        $this->assertDatabaseHas('roles', $data);
    }

    public function test_create_role_invalid_data()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => $role->name,
            'code' => $role->code,
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/roles', $data);

        $response->assertStatus(422);
    }

    public function test_get_role_detail()
    {
        $role = Role::factory()->create();
        $response = $this->getJson("/api/roles/{$role->id}");
        $response->assertOk();
        $response->assertJsonPath('data.name', $role->name);
        $response->assertJsonPath('data.code', $role->code);
    }

    public function test_get_nonexistence_role_detail()
    {
        $response = $this->getJson('/api/roles/101');
        $response->assertNotFound();
    }

    public function test_create_update_role()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $data = [
            'name' => "{$role->name}-updated",
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/roles/{$role->id}", $data);

        $response->assertOk();
        $response->assertJsonPath('data.name', $data['name']);
        $role->refresh();
        $this->assertEquals($data['name'], $role->name);
    }

    public function test_create_update_nonexistence_role()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'role-updated',
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/roles/101', $data);

        $response->assertNotFound();
    }

    public function test_delete_role()
    {
        $role = Role::factory()->create();
        $response = $this->deleteJson("/api/roles/{$role->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }
}
