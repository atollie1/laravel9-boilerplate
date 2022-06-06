<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->words(3, true);
        $userJson = json_encode([
            'id' => 0,
            'name' => 'Programmatic',
        ]);
        return [
            'name' => $name,
            'code' => Str::slug($name),
            'created_by' => $userJson,
            'updated_by' => $userJson,
        ];
    }
}
