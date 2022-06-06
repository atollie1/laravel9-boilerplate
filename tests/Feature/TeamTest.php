<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;

    public function test_get_team_list()
    {
        $teams = Team::factory()->count(5)->create();
        $response = $this->get('/api/teams');

        $response->assertOk();
        $response->assertJsonCount($teams->count(), 'data');
    }

    public function test_create_team()
    {
        $user = User::factory()->create();
        $teamName = $this->faker->words(3, true);
        $data = [
            'name' => $teamName,
            'code' => Str::slug($teamName),
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/teams', $data);
        $response->assertCreated();
        $response->assertJsonPath('data.name', $data['name']);
        $response->assertJsonPath('data.code', $data['code']);
        $this->assertDatabaseHas('teams', $data);
    }

    public function test_create_team_invalid_data()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $data = [
            'name' => $team->name,
            'code' => Str::slug($team->name),
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/teams', $data);
        $response->assertStatus(422);
    }

    public function test_get_team_detail()
    {
        $team = Team::factory()->create();

        $response = $this->getJson("/api/teams/{$team->id}");
        $response->assertOk();
        $response->assertJsonPath('data.name', $team->name);
        $response->assertJsonPath('data.code', $team->code);
    }

    public function test_get_nonexistence_team()
    {
        $response = $this->getJson('/api/teams/101');
        $response->assertNotFound();
    }

    public function test_update_team()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $data = [
            'name' => 'updated',
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/teams/{$team->id}", $data);

        $response->assertOk();
        $response->assertJsonPath('data.name', $data['name']);
        $team->refresh();
        $this->assertEquals($data['name'], $team->name);
    }

    public function test_update_nonexistence_team()
    {
        $user = User::factory()->create();
        $data = [
            'name' => 'updated',
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/teams/101', $data);
        $response->assertNotFound();
    }

    public function test_delete_team()
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson("/api/teams/{$team->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('teams', [
            'id' => $team->id
        ]);
    }

    public function test_delete_nonexistence_team()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson('/api/teams/101');
        $response->assertNotFound();
    }
}
