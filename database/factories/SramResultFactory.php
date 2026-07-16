<?php

namespace Database\Factories;

use App\Models\ResearchVersion;
use App\Models\SramResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SramResult>
 */
class SramResultFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SramResult>
     */
    protected $model = SramResult::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'research_version_id' => ResearchVersion::factory(),
            'overall_score' => fake()->randomFloat(2, 0, 100),
            'overall_result' => fake()->randomElement(['Passed', 'Failed']),
            'recommendation' => fake()->optional()->sentence(),
            'evaluated_at' => now(),
        ];
    }
}