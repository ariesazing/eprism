<?php

namespace Database\Factories;

use App\Models\Research;
use App\Models\ResearchProponent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchProponent>
 */
class ResearchProponentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ResearchProponent>
     */
    protected $model = ResearchProponent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'research_id' => Research::factory(),
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->firstName(),
            'last_name' => fake()->lastName(),
            'suffix' => fake()->optional()->suffix(),
            'position_title' => fake()->optional()->jobTitle(),
            'organizational_unit_name' => fake()->optional()->company(),
            'email' => fake()->optional()->safeEmail(),
            'contact_number' => fake()->optional()->numerify('09#########'),
        ];
    }
}
