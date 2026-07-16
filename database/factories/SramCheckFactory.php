<?php

namespace Database\Factories;

use App\Models\SramCheck;
use App\Models\SramResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SramCheck>
 */
class SramCheckFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SramCheck>
     */
    protected $model = 'App\\Models\\SramCheck';

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sram_result_id' => \App\Models\SramResult::factory(),
            'check_type' => fake()->randomElement(['Completeness', 'Grammar', 'Similarity']),
            'score' => fake()->randomFloat(2, 0, 100),
            'result' => fake()->randomElement(['Passed', 'Failed']),
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}