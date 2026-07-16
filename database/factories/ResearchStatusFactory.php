<?php

namespace Database\Factories;

use App\Models\ResearchStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResearchStatus>
 */
class ResearchStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ResearchStatus>
     */
    protected $model = ResearchStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status_name' => fake()->unique()->words(3, true),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
