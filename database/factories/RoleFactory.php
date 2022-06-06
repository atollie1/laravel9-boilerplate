<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = $this->faker->word;
        $userData = json_encode([
            'id' => 0,
            'name' => 'Programmatic'
        ]);
        return [
            'name' => $name,
            'code' => Str::slug($name),
            'created_by' => $userData,
            'updated_by' => $userData,
        ];
    }
}
